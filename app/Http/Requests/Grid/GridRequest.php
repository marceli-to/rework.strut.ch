<?php

namespace App\Http\Requests\Grid;

use App\Models\GridRow;
use App\Support\GridContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Base for grid requests: resolves context, owner and (optionally) the row
 * from the route, scoped to the owner.
 */
class GridRequest extends FormRequest
{
	protected ?GridContext $gridContext = null;
	protected ?Model $gridOwner = null;

	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [];
	}

	public function context(): GridContext
	{
		return $this->gridContext ??= GridContext::for($this->route('context'));
	}

	public function owner(): Model
	{
		return $this->gridOwner ??= $this->context()->owner($this->route('owner'));
	}

	public function row(?string $uuid = null): GridRow
	{
		return $this->owner()->gridRows()->where('uuid', $uuid ?? $this->route('row'))->firstOrFail();
	}
}
