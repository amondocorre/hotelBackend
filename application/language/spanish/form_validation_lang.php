<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Mensajes de error comunes
$lang['form_validation_required'] = 'El campo {field} es obligatorio.';
$lang['form_validation_isset'] = 'El campo {field} debe contener un valor.';
$lang['form_validation_valid_email'] = 'El campo {field} debe contener una dirección de correo electrónico válida.';
$lang['form_validation_valid_url'] = 'El campo {field} debe contener una URL válida.';
$lang['form_validation_valid_ip'] = 'El campo {field} debe contener una dirección IP válida.';
$lang['form_validation_min_length'] = 'El campo {field} debe tener al menos {param} caracteres de longitud.';
$lang['form_validation_max_length'] = 'El campo {field} no puede exceder los {param} caracteres de longitud.';
$lang['form_validation_exact_length'] = 'El campo {field} debe tener exactamente {param} caracteres de longitud.';
$lang['form_validation_alpha'] = 'El campo {field} solo puede contener caracteres alfabéticos.';
$lang['form_validation_alpha_numeric'] = 'El campo {field} solo puede contener caracteres alfanuméricos.';
$lang['form_validation_alpha_numeric_spaces'] = 'El campo {field} solo puede contener caracteres alfanuméricos y espacios.';
$lang['form_validation_alpha_dash'] = 'El campo {field} solo puede contener caracteres alfanuméricos, guiones bajos y guiones.';
$lang['form_validation_numeric'] = 'El campo {field} debe contener solo números.';
$lang['form_validation_integer'] = 'El campo {field} debe contener un entero.';
$lang['form_validation_decimal'] = 'El campo {field} debe contener un número decimal.';
$lang['form_validation_natural'] = 'El campo {field} debe contener solo números naturales.';
$lang['form_validation_natural_no_zero'] = 'El campo {field} debe contener solo números naturales mayores que cero.';
$lang['form_validation_matches'] = 'El campo {field} no coincide con el campo {param}.';
$lang['form_validation_differs'] = 'El campo {field} debe ser diferente del campo {param}.';
$lang['form_validation_in_list'] = 'El campo {field} debe ser uno de: {param}.';
$lang['form_validation_regex_match'] = 'El campo {field} no tiene el formato correcto.';
$lang['form_validation_less_than'] = 'El campo {field} debe contener un número menor que {param}.';
$lang['form_validation_less_than_equal_to'] = 'El campo {field} debe contener un número menor o igual que {param}.';
$lang['form_validation_greater_than'] = 'El campo {field} debe contener un número mayor que {param}.';
$lang['form_validation_greater_than_equal_to'] = 'El campo {field} debe contener un número mayor o igual que {param}.';
$lang['form_validation_is_unique'] = 'El campo {field} ya existe.';

// Validaciones de fecha
$lang['form_validation_valid_date'] = 'El campo {field} debe contener una fecha válida.';
$lang['form_validation_valid_date_format'] = 'El campo {field} debe tener el formato de fecha {param}.';

// Validaciones de archivos
$lang['form_validation_max_size'] = 'El archivo {field} no puede exceder los {param} kilobytes.';
$lang['form_validation_mime_in'] = 'El archivo {field} debe ser de uno de los siguientes tipos: {param}.';
$lang['form_validation_ext_in'] = 'El archivo {field} debe tener una de las siguientes extensiones: {param}.';

//validaciones perosnalizados
$lang['perfil_existe'] = 'El perfil seleccionado no existe.';
$lang['email_unique_current'] = 'Este correo electrónico ya está en uso por otro usuario.';
$lang['usuario_unique_current'] = 'Este Usuario ya está en uso.';
$lang['perfil_unique_current'] = 'Este nombe de perfil ya está en uso.';
$lang['email_unique_client'] = 'Este correo electrónico ya está en uso por otro Cliente.';

?>