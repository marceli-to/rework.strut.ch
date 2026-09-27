<x-layout.site :description="$seo?->contact_meta_description" :og-image="asset('img/forrerzimmermann-anfahrtsplan.jpg')" title="Kontakt">

  <div class="h-full md:px-16 xl:px-32">

    <x-grid.container class="h-full">
      
      <x-grid.span class="md:col-span-7 xl:col-span-8 md:-ml-16 xl:-ml-32 md:min-h-0">
        <a
          href="{{ $contact?->maps_url }}"
          target="_blank"
          rel="noopener noreferrer"
          aria-label="Anfahrtsplan auf Google Maps öffnen"
          class="block w-full h-full">
          <picture>
            <source srcset="{{ asset('img/forrerzimmermann-anfahrtsplan.avif') }}" type="image/avif">
            <source srcset="{{ asset('img/forrerzimmermann-anfahrtsplan.webp') }}" type="image/webp">
            <img
              src="{{ asset('img/forrerzimmermann-anfahrtsplan.jpg') }}"
              alt="Anfahrtsplan Forrer Zimmermann"
              width="891"
              height="587"
              loading="eager"
              class="aspect-[4/3] md:aspect-auto w-full h-full object-cover">
          </picture>
        </a>
      </x-grid.span>

      <x-grid.span class="md:col-span-5 xl:col-span-4 px-16 md:pl-0 md:pr-16 xl:pr-32 md:-mr-16 xl:-mr-32 min-h-0 overflow-auto">

        @if($contact)
          <article class="hyphens-auto py-18">

            <h1 class="text-2xl leading-[1.174] mb-16">
              {{ $contact->name }}
            </h1>

            <div class="text-lg leading-[1.33]">
              <div class="mb-48">
                {!! nl2br($contact->address) !!}
                @if($contact->email)
                  <br>
                  <a
                    href="mailto:{{ $contact->email }}"
                    class="hover:text-accent transition-colors !no-underline"
                    aria-label="E-Mail an {{ $contact->email }} senden">
                    {{ $contact->email }}
                  </a>
                @endif
                @if($contact->phone)
                  <br>
                  <a
                    href="tel:{{ $contact->phone }}"
                    class="hover:text-accent transition-colors !no-underline"
                    aria-label="Anrufen unter {{ $contact->phone }}">
                    {{ $contact->phone }}
                  </a>
                @endif
              </div>

              @if ($contact->imprint)
                <div x-data="{ open: false }" class="mb-24">
                  <x-buttons.toggle label="Impressum" />
                  <div
                    x-cloak
                    x-show="open"
                    class="mt-18">
                    {!! $contact->imprint !!}
                  </div>
                </div>
              @endif

              <div x-data="{ open: false }">
                <x-buttons.toggle label="Datenschutzerklärung" />
                <div
                  x-cloak
                  x-show="open"
                  class="mt-18 privacy">
                  <p>Die Betreiber dieser Seiten nehmen den Schutz Ihrer persönlichen Daten sehr ernst. Wir behandeln Ihre personenbezogenen Daten vertraulich und entsprechend der gesetzlichen Datenschutzvorschriften sowie dieser Datenschutzerklärung.</p>
                  <h2>Cookies</h2>
                  <p>Die Internetseiten verwenden teilweise so genannte Cookies. Cookies richten auf Ihrem Rechner keinen Schaden an und enthalten keine Viren. Cookies dienen dazu, unser Angebot nutzerfreundlicher, effektiver und sicherer zu machen. Cookies sind kleine Textdateien, die auf Ihrem Rechner abgelegt werden und die Ihr Browser speichert.</p>
                  <p>Die meisten der von uns verwendeten Cookies sind so genannte „Session-Cookies". Sie werden nach Ende Ihres Besuchs automatisch gelöscht. Andere Cookies bleiben auf Ihrem Endgerät gespeichert, bis Sie diese löschen. Diese Cookies ermöglichen es uns, Ihren Browser beim nächsten Besuch wiederzuerkennen.</p>
                  <p>Sie können Ihren Browser so einstellen, dass Sie über das Setzen von Cookies informiert werden und Cookies nur im Einzelfall erlauben, die Annahme von Cookies für bestimmte Fälle oder generell ausschliessen sowie das automatische Löschen der Cookies beim Schliessen des Browser aktivieren. Bei der Deaktivierung von Cookies kann die Funktionalität dieser Website eingeschränkt sein.</p>
                  <h2>Hosting Provider & Server-LogFiles</h2>
                  <p>Der Provider der Seiten erhebt und speichert automatisch Informationen in so genannten Server-Log Files, die Ihr Browser automatisch an uns übermittelt. Dies sind:</p>
                  <ul>
                    <li>IP-Adresse</li>
                    <li>Browsertyp und Browserversion</li>
                    <li>verwendetes Betriebssystem</li>
                    <li>Referrer URL</li>
                    <li>Hostname des zugreifenden Rechners</li>
                    <li>Uhrzeit der Serveranfrage</li>
                  </ul>
                  <p>Diese Daten können nicht direkt bestimmten Personen zugeordnet werden. Eine Zusammenführung dieser Daten mit anderen Datenquellen wird nicht vorgenommen. Wir behalten uns vor, diese Daten nachträglich zu prüfen, wenn uns konkrete Anhaltspunkte für eine rechtswidrige Nutzung bekannt werden.</p>
                  <p>Diese Daten sowie alle Daten dieser Website werden bei unserem Hosting-Provider hosttech GmbH, 8805 Richterswil, Schweiz gespeichert, deren Datenschutzerklärung Sie <a href="https://www.hosttech.ch/datenschutz/" target="_blank" rel="noopener noreferrer" class="hover:text-accent transition-colors !no-underline" aria-label="Datenschutzerklärung von hosttech öffnen">hier</a> finden.</p>
                  <h2>Dauer der Speicherung und Widerspruchsrecht</h2>
                  <p>Sie haben jederzeit die Möglichkeit, Ihre Einwilligung zur Verarbeitung der personenbezogenen Daten per E-Mail an <a href="mailto:mail@forrerzimmermann.ch" target="_blank" rel="noopener noreferrer" class="hover:text-accent transition-colors !no-underline" aria-label="E-Mail an mail@forrerzimmermann.ch senden">mail@forrerzimmermann.ch</a> zu widerrufen. In einem solchen Fall kann die Konversation nicht fortgeführt werden.</p>
                  <p>Alle personenbezogenen Daten, die im Zuge der Kontaktaufnahme gespeichert wurden, werden in diesem Fall gelöscht.</p>
                  <h2>Recht auf Auskunft, Löschung, Sperrung</h2>
                  <p>Sie haben jederzeit das Recht auf unentgeltliche Auskunft über Ihre gespeicherten personenbezogenen Daten, deren Herkunft und Empfänger und den Zweck der Datenverarbeitung sowie ein Recht auf Berichtigung, Sperrung oder Löschung dieser Daten. Hierzu sowie zu weiteren Fragen zum Thema personenbezogene Daten können Sie sich jederzeit unter der im Impressum angegebenen Adresse an uns wenden oder eine E-Mail an <a href="mailto:mail@forrerzimmermann.ch" target="_blank" rel="noopener noreferrer" class="hover:text-accent transition-colors !no-underline" aria-label="E-Mail an mail@forrerzimmermann.ch senden">mail@forrerzimmermann.ch</a> senden.</p>
                  <p>Für weitere Informationen zur Verarbeitung Ihrer personenbezogenen Daten und zur Ausübung Ihrer Rechte im Zusammenhang mit der Nutzung dieser Webseite lesen Sie bitte die Datenschutzerklärung.</p>
                </div>
              </div>

            </div>
          </article>
        @endif
      </x-grid.span>
      
    </x-grid.container>
  </div>
  
  <x-slot:footer>
    <a 
      href="{{ $contact->maps_url }}" 
      class="text-lg leading-[1.33] hover:text-accent transition-colors !no-underline"
      target="_blank"
      rel="noopener noreferrer"
      aria-label="Google Maps">
      Google Maps
    </a>
  </x-slot>

</x-layout.site>
