<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    {{--  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>  --}}
    <style type="text/css">
          body{
            background-color: #ffffff; /* Color plomo */
          }

          .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
          }

          .line {
            margin-bottom: 10px; /* Espacio entre las líneas */
            padding: 10px;
            text-align: justify;
            font-weight: bold;
          }
          .titulo{
            font-size: 25px;
            color: #09366f;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;

          }

          .hijo{
            width: 500px;
            background-color: #ffffff;
            padding: 25px;
            border: 10px solid #f3451a; /* Borde azul */
            box-shadow: 0 0 0 10px #d7d7d7; /* Borde plomo (sombra) */
          }
          .tituloFooter{
            font-size: 25px;
            color: white;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            text-align: center
          }
    </style>
</head>
<body>
    <div class="container">
        <div class="hijo">
            <div class="line">
                <center>
                    <img src="https://goghu.net/micarLogo.png" alt="aqui va el Logo de KENNEL" width="50%">
                </center>
            </div>
            <div class="titulo">
                MI CAR AUTO LAVADO
            </div>
            <hr>
            <div class="line">
                Hola {{ $name }}
            </div>
            <div class="line">
                ¡Gracias por confiar en nosotros!
                Adjunto a este correo, encontrarás tu
                Factura Computarizada Electronica en Línea (Representación Gráfica)
                con N° {{ $number }} emitida en {{ $date }} y la información de
                la transacción en formato XML.
            </div>
            <div class="line">
                Cualquier consulta respecto a la factura, no dudes
                realizarla dentro del mes de su emisión y a través
                de nuestros canales de contacto.
            </div>
            <div class="line">
                ¡Que tengas un gran día!
            </div>
        </div>
        <br>
        <hr>
        <br>
        {{-- <div class="tituloFooter">
            SOMOS ESPECIALISTAS EN LA FORMACION DE CONTADORES GENERALES
        </div> --}}
        {{-- <div class="line">
            <a href="https://wa.link/wbeou8">WhatsApp</a> | <a href="https://www.facebook.com/EFGIPET">Facebook</a> | <a href="https://ef-gipet.edu.bo/">ef-gipet.edu.bo</a>
        </div> --}}
    </div>
</body>
</html>
