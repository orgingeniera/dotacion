<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejemplo con Select2</title>

    <!-- Cargar CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Cargar jQuery primero -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>

    <!-- Cargar JS de Select2 después de jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <h1>Ejemplo Select2</h1>
    <label for="select-producto">Selecciona un estado:</label>
    <select class="js-example-basic-single" id="select-producto" name="states[]" style="width: 50%;">
        <option value="AL">Alabama</option>
        <option value="WY">Wyoming</option>
        <option value="NY">New York</option>
        <option value="CA">California</option>
    </select>

    <script>
        $(document).ready(function () {
            // Inicializar Select2
            $('.js-example-basic-single').select2({
                placeholder: "Selecciona un estado", // Texto de ayuda
                allowClear: true // Permite limpiar la selección
            });
        });
    </script>
</body>
</html>
