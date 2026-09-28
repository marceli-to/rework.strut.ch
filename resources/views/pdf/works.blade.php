{{--
  Werkliste PDF (dompdf). Styles and markup are the legacy
  resources/views/web/pdf/partials/header.blade.php; fonts and logo come from
  resources/pdf. Content: GetWorksPdf (sections → groups → lines).
--}}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>strut.ch - {{ $title }}</title>
<style>
@font-face {
    font-family: 'basis-grotesque-regular-pro';
    src: url('{{ resource_path("pdf/basis-grotesque-regular-pro.ttf") }}') format("truetype");
    font-weight: normal;
    font-style: normal; 
}

@font-face {
    font-family: 'basis-grotesque-medium-pro';
    src: url('{{ resource_path("pdf/basis-grotesque-medium-pro.ttf") }}') format("truetype");
    font-weight: normal;
    font-style: normal; 
}

@page {
  size: A4;
  margin: 0;
}

@media print {
  html, body {
    width: 210mm;
    height: 297mm;
  }
}

html {
    margin: 0;
    padding: 0;
}

body {
    background-color: white;
    padding: 71mm 30mm 30mm 40mm;
    line-height: 1 !important;
}

* {
    font-family: 'basis-grotesque-regular-pro', sans-serif !important;
    font-style: normal;
    font-stretch: normal;
    font-weight: normal;
    text-rendering: optimizeLegibility;
    color: #000000;
}

header {
    position: fixed;
    top: 14mm;
    right: 18mm;
    text-align: right;
    width: 100%;
}

.logo {
    display: inline-block;
}

footer {
    color: #000000;
    font-size: 12pt !important;
    line-height: 9pt !important;
    top: 277mm;
    position: absolute;
    /* bottom: 20.1mm;
    position: fixed; */
    letter-spacing: 0.1mm !important;
    left: 6.8mm;
}

.date,
.title,
.content-title,
.content-items {
    color: #000000;
    display: block;
    font-size: 9.5pt !important;
    line-height: 1.2 !important;
    letter-spacing: 0.05mm !important;
}

.date {
    margin-bottom: 4mm;
}


strong,
b,
.title,
.content-title {
    font-family: 'basis-grotesque-medium-pro', sans-serif !important;
    font-style: normal;
    font-stretch: normal;
    font-weight: normal;
}

.title {
    margin-bottom: 7mm;
}

.content-title {
    border-bottom: 0.2mm solid #000000;
    padding-bottom: .5mm;
    margin-bottom: 3mm;
    width: 100%;
}

.content-items {
    line-height: 0.835 !important;
    margin-bottom: 7mm;
}

.content-item {
    display: block;
}

.date,
.title {
    position: absolute;
}

.date {
    top: -21mm;
}

.title {
    top: -10.8mm;
}

</style>
</head>
<body>
<header>
    <img src="{{ resource_path('pdf/logo-strut.svg') }}" class="logo" height="100" width="196">
</header>
<footer>Strut Architekten AG<br>Neuwiesenstrasse 69, 8400 Winterthur<br>052 213 33 60, mail@strut.ch, www.strut.ch</footer>
<span class="date">{{ $date }}</span>
<span class="title">{{ $title }}</span>
<div class="content">
  @foreach ($sections as $section)
    <span class="content-title">{{ $section['title'] }}</span>
    <div class="content-items">
      @foreach ($section['groups'] as $group)
        @if ($group['heading'])
          <div class="content-item"><strong>{{ $group['heading'] }}</strong></div>
        @endif
        @foreach ($group['lines'] as $line)
          <div class="content-item">{{ $line }}</div>
        @endforeach
        @if ($group['break'])
          <br>
        @endif
      @endforeach
    </div>
  @endforeach
</div>
</body>
</html>
