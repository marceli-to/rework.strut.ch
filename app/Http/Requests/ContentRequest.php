<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HasMediaRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Base request for admin content forms (store and update share the rules).
 */
abstract class ContentRequest extends FormRequest
{
	use HasMediaRules;

	/**
	 * Fields sent as uuid by the admin and stored as id: ['field' => Model::class].
	 */
	protected array $uuidFields = [];

	/**
	 * Whether the form sends newly uploaded media.
	 */
	protected bool $withMedia = true;

	/**
	 * Rules for the model fields. Subclasses also define attributes() with the
	 * German field names used by lang/de/validation.php.
	 */
	abstract protected function fields(): array;

	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [...$this->fields(), ...($this->withMedia ? $this->mediaRules() : [])];
	}

	/**
	 * Validated data ready for the model (uuids resolved to ids).
	 */
	public function payload(): array
	{
		$data = $this->validated();

		foreach ($this->uuidFields as $field => $model) {
			if (array_key_exists($field, $data)) {
				$data[$field] = $data[$field] ? $model::where('uuid', $data[$field])->value('id') : null;
			}
		}

		return $data;
	}
}
