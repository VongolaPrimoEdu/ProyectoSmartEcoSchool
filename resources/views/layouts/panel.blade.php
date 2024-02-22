<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>@yield("title", "Panel SES")</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet"
  crossorigin="anonymous" />
	<link rel="stylesheet" href="{{asset("css/panel.css")}}">
</head>
<body>
	<h1>Panel de control de SmartEcoSchool</h1>
	<div class="row g-0">
    <!-- sidebar -->
    <div class="p-3 col fixed text-white bg-dark">
      <hr />
      <ul class="nav flex-column">
        <li><a class="nav-link text-white">- Página principal de la administración</a></li>
        <li><a class="nav-link text-white">- Administración de productos</a></li>
        <li><a class="mt-2 btn bg-primary text-white">Volver a la página principal</a></li>
      </ul>
    </div>
    <!-- sidebar -->
    <div class="col content-grey">
      <div class="g-0 m-5">
        @yield('content')
      </div>
    </div>
  </div>
  <!-- footer -->
  <div class="copyright py-4 text-center text-white">
    <div class="container">
      <small>
        Pie de página
      </small>
    </div>
  </div>
  <!-- footer -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
  </script>
</body>
</html>