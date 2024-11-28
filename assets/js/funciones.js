document.addEventListener("DOMContentLoaded", function () {
    $('#tbl').DataTable({
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
        },
        "order": [
            [0, "desc"]
        ]
    });
    $(".confirmar").submit(function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Esta seguro de eliminar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'SI, Eliminar!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        })
    })
    $("#documentos").on('keypress', function (e) {
        if (e.which === 13) { // 13 es el código de la tecla Enter
            var documento = $(this).val(); // Obtiene el valor del campo de texto
    
            // Realiza la solicitud Ajax solo cuando se presiona Enter
            $.ajax({
                url: "ajax.php", // El archivo PHP que va a manejar la solicitud
                dataType: "json",
                data: {
                    q: documento // Se pasa el valor del campo 'documento' a la consulta
                },
                success: function (data) {
                    // Verifica si la respuesta contiene datos
                    if (data.length > 0) {
                        var ui = data[0]; // Usamos el primer resultado, puedes ajustar si es necesario
                        $("#idcliente").val(ui.id);
                        $("#nombre").val(ui.label);
                        $("#documento").val(ui.documento);
                        $("#dotaciones").empty();
                        // Agregar las opciones al select con los valores de dotación
                        data.forEach(function(item) {
                            // Agregar cada opción al select
                            $("#dotaciones").append(new Option('Dotación ' + item.dotacion, item.dotacion));
                        });
                       
                    } else {
                        alert("No se encontró ningún resultado.");
                    }
                }
            });
        }
    });
    
   /* $('#producto').on('change', function () {
        var codigoProducto = $(this).val(); // Obtener el valor seleccionado
        if (codigoProducto) {
       
            // Realizar solicitud Ajax para buscar los datos del producto
            $.ajax({
                url: "ajax.php", // Endpoint para buscar detalles del producto
                dataType: "json",
                data: {
                    pro: codigoProducto // Enviar el código del producto seleccionado
                },
                success: function (data) {
                 
                    if (data) {
                        $("#id").val(data.id);
                        $("#stock").val(data.existencia);
                        $("#cantidad").focus();
                    } else {
                        alert("No se encontró ningún producto con ese código.");
                    }
                },
                error: function () {
                    alert("Error al buscar los datos del producto.");
                }
            });
        }
    });*/
    

    $('#btn_generar').click(function (e) {
       
        e.preventDefault();
        var rows = $('#tblDetalle tr').length;
        if($('#documentos').val() == ""){
            alert("Ingrese un docente por favor.")
        }else{   
        if (rows > 2) 
        {
                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "¡No podrás revertir esta acción!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sí, procesar venta",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si el usuario confirma, continuar con la lógica de procesar la venta
                        var action = 'procesarVenta';
                        var dotaciones = $('#dotaciones').val();
                        var id = $('#idcliente').val();
                        var documentos = $('#documentos').val();
                        $.ajax({
                            url: 'ajax.php',
                            async: true,
                            data: {
                                procesarVenta: action,
                                dotaciones: dotaciones,
                                documentos: documentos,
                                id: id
                            },
                            success: function (response) {
                                const res = JSON.parse(response);
                                if (res.mensaje != '1' && res.mensaje != '2') {
                                    Swal.fire({
                                        position: 'top-end',
                                        icon: 'success',
                                        title: 'Venta Generada',
                                        showConfirmButton: false,
                                        timer: 2000
                                    });
                                    setTimeout(() => {
                                        generarPDF(res.id_cliente, res.id_venta);
                                        location.reload();
                                    }, 300);
                                } else {
                                    if (res.mensaje == '1') {
                                        Swal.fire({
                                            position: 'top-end',
                                            icon: 'error',
                                            title: 'Error al generar la venta',
                                            showConfirmButton: false,
                                            timer: 2000
                                        });
                                    } else {
                                        Swal.fire({
                                            position: 'top-end',
                                            icon: 'error',
                                            title: 'Esta dotación para este docente ya está ingresada',
                                            showConfirmButton: false,
                                            timer: 3000
                                        });
                                    }
                                }
                            },
                            error: function (error) {
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'error',
                                    title: 'Error en la solicitud',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            }
                        });
                    }
                });
            } else {
                Swal.fire({
                    position: 'top-end',
                    icon: 'warning',
                    title: 'No hay producto para generar la venta',
                    showConfirmButton: false,
                    timer: 2000
                });
            }

        }

    });
    if (document.getElementById("detalle_venta")) {
        listar();
    }
})

function borrarTemp() {
    let borrartemp = 'borrartemp';
    $.ajax({
        url: "ajax.php",
        type: 'GET',
        dataType: "json",
        data: {
            borrartemp: borrartemp
        },
        success: function (response) {
            if (response.status === 'success') {
              //  alert(response.message); // Mensaje opcional para confirmar
                listar(); // Actualiza la lista
            } else {
              //  alert(response.message); // Mensaje de error
            }
        },
        error: function (xhr, status, error) {
            console.error('Error en la solicitud:', error);
            alert('Ocurrió un error al eliminar el registro.');
        }
    });
}


function calcularPrecio(e) {
    e.preventDefault();
    const cant = $("#cantidad").val();
    const stock = $('#stock').val();
    if (parseInt(cant) > parseInt(stock)) {
        // Si la cantidad es mayor que el stock, mostrar mensaje
        Swal.fire({
            position: 'top-end',
            icon: 'error', // Cambié el icono a 'error' para que sea más claro
            title: 'La cantidad no puede ser mayor que la existencia del producto',
            showConfirmButton: false,
            timer: 3000
        });
        $('#cantidad').focus(); // Volver a enfocar el campo cantidad
        return false; // Detener la ejecución del resto del código
    }
    const total = stock - cant;
    $('#sub_total').val(total);
    if (e.which == 13) {
        if (cant > 0 && cant != '') {
            const id = $('#id').val();
            registrarDetalle(e, id, cant, stock);
            $('#producto').focus();
        } else {
            $('#cantidad').focus();
            return false;
        }
    }
}

function calcularDescuento(e, id) {
    if (e.which == 13) {
        let descuento = 'descuento';
        $.ajax({
            url: "ajax.php",
            type: 'GET',
            dataType: "json",
            data: {
                id: id,
                desc: e.target.value,
                descuento: descuento
            },
            success: function (response) {

                if (response.mensaje == 'descontado') {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Descuento Aplicado',
                        showConfirmButton: false,
                        timer: 2000
                    })
                    listar();
                } else {}
            }
        });
    }
}

function listar() {
    let html = '';
    let detalle = 'detalle';
    $.ajax({
        url: "ajax.php",
        dataType: "json",
        data: {
            detalle: detalle
        },
        success: function (response) {

            response.forEach(row => {
                html += `<tr>
                <td>${row['id']}</td>
                <td>${row['descripcion']}</td>
                <td>${row['cantidad']}</td>
                <td>${row['talla']}</td>
                <td><button class="btn btn-danger" type="button" onclick="deleteDetalle(${row['id']})">
                <i class="fas fa-trash-alt"></i></button></td>
                </tr>`;
            });
            document.querySelector("#detalle_venta").innerHTML = html;
            calcular();
        }
    });
}

function registrarDetalle(e, id, cant, stock) {
    if (document.getElementById('producto').value != '') {
       
        if (id != null) {
            let action = 'regDetalle';
            $.ajax({
                url: "ajax.php",
                type: 'POST',
                dataType: "json",
                data: {
                    id: id,
                    cant: cant,
                    regDetalle: action,
                    stock: stock
                },
                success: function (response) {

                    if (response == 'registrado') {
                        $('#cantidad').val('');
                        $('#stock').val('');
                        $("#producto").val('');
                        $("#sub_total").val('');
                        $("#producto").focus();
                        listar();
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Producto Ingresado',
                            showConfirmButton: false,
                            timer: 1000
                        })
                    } else if (response == 'actualizado') {
                        $('#cantidad').val('');
                        $('#stock').val('');
                        $("#producto").val('');
                        $("#producto").focus();
                        listar();
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Producto Actualizado',
                            showConfirmButton: false,
                            timer: 2000
                        })
                    }else if (response == 'errorexistencia') {
                        $('#cantidad').val('');
                        $('#stock').val('');
                        $("#producto").val('');
                        $("#producto").focus();
                        listar();
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: ' La existencia disponible no es suficiente para la cantidad requerida.',
                            showConfirmButton: false,
                            timer: 4000
                        })
                    } else {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error al ingresar el producto',
                            showConfirmButton: false,
                            timer: 2000
                        })
                    }
                }
            });
        }
    }
}

function deleteDetalle(id) {
    let detalle = 'Eliminar'
    $.ajax({
        url: "ajax.php",
        data: {
            id: id,
            delete_detalle: detalle
        },
        success: function (response) {

            if (response == 'restado') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Producto Descontado',
                    showConfirmButton: false,
                    timer: 2000
                })
                document.querySelector("#producto").value = '';
                document.querySelector("#producto").focus();
                listar();
            } else if (response == 'ok') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Producto Eliminado',
                    showConfirmButton: false,
                    timer: 2000
                })
                document.querySelector("#producto").value = '';
                document.querySelector("#producto").focus();
                listar();
            } else {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error al eliminar el producto',
                    showConfirmButton: false,
                    timer: 2000
                })
            }
        }
    });
}

function calcular() {
    // obtenemos todas las filas del tbody
    var filas = document.querySelectorAll("#tblDetalle tbody tr");

    var total = 0;

    // recorremos cada una de las filas
    filas.forEach(function (e) {

        // obtenemos las columnas de cada fila
        var columnas = e.querySelectorAll("td");

        // obtenemos los valores de la cantidad y importe
        var importe = parseFloat(columnas[6].textContent);

        total += importe;
    });

    // mostramos la suma total
    var filas = document.querySelectorAll("#tblDetalle tfoot tr td");
    filas[1].textContent = total.toFixed(2);
}

function generarPDF(cliente, id_venta) {
    url = 'pdf/generar.php?cl=' + cliente + '&v=' + id_venta;
    window.open(url, '_blank');
}
if (document.getElementById("stockMinimo")) {
    const action = "sales";
    $.ajax({
        url: 'chart.php',
        type: 'POST',
        data: {
            action
        },
        async: true,
        success: function (response) {
            if (response != 0) {
                var data = JSON.parse(response);
                var nombre = [];
                var cantidad = [];
                for (var i = 0; i < data.length; i++) {
                    nombre.push(data[i]['codigo']);
                    cantidad.push(data[i]['existencia']);
                }
                var ctx = document.getElementById("stockMinimo");
                var myPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: nombre,
                        datasets: [{
                            data: cantidad,
                            backgroundColor: ['#024A86', '#E7D40A', '#581845', '#C82A54', '#EF280F', '#8C4966', '#FF689D', '#E36B2C', '#69C36D', '#23BAC4'],
                        }],
                    },
                });
            }
        },
        error: function (error) {
            console.log(error);
        }
    });
}
if (document.getElementById("ProductosVendidos")) {
    const action = "polarChart";
    $.ajax({
        url: 'chart.php',
        type: 'POST',
        async: true,
        data: {
            action
        },
        success: function (response) {
            if (response != 0) {
                var data = JSON.parse(response);
                var nombre = [];
                var cantidad = [];
                for (var i = 0; i < data.length; i++) {
                    nombre.push(data[i]['codigo']);
                    cantidad.push(data[i]['cantidad']);
                }
                var ctx = document.getElementById("ProductosVendidos");
                var myPieChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: nombre,
                        datasets: [{
                            data: cantidad,
                            backgroundColor: ['#C82A54', '#EF280F', '#23BAC4', '#8C4966', '#FF689D', '#E7D40A', '#E36B2C', '#69C36D', '#581845', '#024A86'],
                        }],
                    },
                });
            }
        },
        error: function (error) {
            console.log(error);

        }
    });
}

function btnCambiar(e) {
    e.preventDefault();
    const actual = document.getElementById('actual').value;
    const nueva = document.getElementById('nueva').value;
    if (actual == "" || nueva == "") {
        Swal.fire({
            position: 'top-end',
            icon: 'error',
            title: 'Los campos estan vacios',
            showConfirmButton: false,
            timer: 2000
        })
    } else {
        const cambio = 'pass';
        $.ajax({
            url: "ajax.php",
            type: 'POST',
            data: {
                actual: actual,
                nueva: nueva,
                cambio: cambio
            },
            success: function (response) {
                if (response == 'ok') {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Contraseña modificado',
                        showConfirmButton: false,
                        timer: 2000
                    })
                    document.querySelector('#frmPass').reset();
                    $("#nuevo_pass").modal("hide");
                } else if (response == 'dif') {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'La contraseña actual incorrecta',
                        showConfirmButton: false,
                        timer: 2000
                    })
                } else {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'Error al modificar la contraseña',
                        showConfirmButton: false,
                        timer: 2000
                    })
                }
            }
        });
    }
}
function editarClientess(id) {
    const action = "editarCliente";
    $.ajax({
        url: 'ajax.php',
        type: 'GET',
        async: true,
        data: {
            editarCliente: action,
            id: id
        },
        success: function (response) {
            const datos = JSON.parse(response);

            $('#nombre').val(datos.nombre);
            $('#documento').val(datos.documento);
            $('#id_municipios').val(datos.id_municipios);
            $('#sexo').val(datos.sexo);
            $('#tipo_funcionario').val(datos.tipo_funcionario);
            $('#dotacion').val(datos.dotacion);
            $('#bono').val(datos.bono);
            $('#id').val(datos.idcliente);

            // Cargar colegios del municipio seleccionado
            fetch(`get_colegios.php?id_municipios=${datos.id_municipios}`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Seleccione un colegio</option>';
                    data.forEach(colegio => {
                        options += `<option value="${colegio.id_colegios}">${colegio.nombre}</option>`;
                    });
                    $('#id_colegio').html(options);
                    $('#id_colegio').val(datos.id_colegio);
                })
                .catch(error => console.error('Error:', error));
        },
        error: function (error) {
            console.log(error);
        }
    });
}
function editarProveedor(id) {

    const action = "editarProveedor";
    $.ajax({
        url: 'ajax.php',
        type: 'GET',
        async: true,
        data: {
            editarProveedor: action,
            id: id
        },
        success: function (response) {
            const datos = JSON.parse(response);
          
            $('#nombre').val(datos.nombre);
            $('#apellido').val(datos.apellido);
            $('#telefono').val(datos.telefono);
            $('#direccion').val(datos.direccion);
            $('#descripcion').val(datos.descripcion);
            $('#id').val(datos.idproveedor); // Campo actualizado para proveedores
            $('#btnAccion').val('Modificar');
        },
        error: function (error) {
            console.log(error);
        }
    });
}

function editarUsuario(id) {
    const action = "editarUsuario";
    $.ajax({
        url: 'ajax.php',
        type: 'GET',
        async: true,
        data: {
            editarUsuario: action,
            id: id
        },
        success: function (response) {
            const datos = JSON.parse(response);
            $('#nombre').val(datos.nombre);
            $('#usuario').val(datos.usuario);
            $('#correo').val(datos.correo);
            $('#id').val(datos.idusuario);
            $('#btnAccion').val('Modificar');
        },
        error: function (error) {
            console.log(error);

        }
    });
}


function limpiar() {
    $('#formulario')[0].reset();
    $('#id').val('');
    $('#btnAccion').val('Registrar');
}