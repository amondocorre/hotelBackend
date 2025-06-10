<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['api/login'] = 'UserController/login';
$route['api/logout'] = 'UserController/logout';
$route['api/getMenuAccess'] = 'UserController/getMenuAccess';
$route['api/user/create_user'] = 'UserController/create_user';
$route['api/user/update_user/(:num)'] = 'UserController/update_user/$1';
$route['api/user/delete/(:num)'] = 'UserController/delete/$1';
$route['api/user/activate/(:num)'] = 'UserController/activate/$1';
$route['api/user/getAllUsers'] = 'UserController/getAllUsers';
$route['api/user/findActive'] = 'UserController/findActive';
$route['api/user/setStateUser/(:num)'] = 'UserController/setStateUser/$1';
$route['api/user/getButtonsAccesUser/(:num)'] = 'UserController/getButtonsAccesUser/$1';
//perfiles
$route['api/perfil/getPerfil'] = 'PerfilController/getPerfil';
$route['api/perfil/findAllPerfil'] = 'PerfilController/findAllPerfil';
$route['api/perfil/create'] = 'PerfilController/create';
$route['api/perfil/update/(:any)'] = 'PerfilController/update/$1';
$route['api/perfil/delete/(:any)'] = 'PerfilController/delete/$1';
$route['api/perfil/activate/(:any)'] = 'PerfilController/activate/$1';

//client
$route['api/client/findActive'] = 'ClientController/findActive';
$route['api/client/findAll'] = 'ClientController/findAll';
$route['api/client/create'] = 'ClientController/create';
$route['api/client/update/(:any)'] = 'ClientController/update/$1';
$route['api/client/delete/(:any)'] = 'ClientController/delete/$1';
$route['api/client/activate/(:any)'] = 'ClientController/activate/$1';

//access
$route['api/config/access/getPerfil'] = 'configurations/MenuAccessController/getClient';
$route['api/config/access/findAll'] = 'configurations/MenuAccessController/findAll';
$route['api/config/access/create'] = 'configurations/MenuAccessController/create';
$route['api/config/access/update/(:any)'] = 'configurations/MenuAccessController/update/$1';
$route['api/config/access/delete/(:any)'] = 'configurations/MenuAccessController/delete/$1';
$route['api/config/access/activate/(:any)'] = 'configurations/MenuAccessController/activate/$1';

//Button
$route['api/config/button/findActive'] = 'configurations/ButtonController/findActive';
$route['api/config/button/findAll'] = 'configurations/ButtonController/findAll';
$route['api/config/button/create'] = 'configurations/ButtonController/create';
$route['api/config/button/update/(:any)'] = 'configurations/ButtonController/update/$1';
$route['api/config/button/delete/(:any)'] = 'configurations/ButtonController/delete/$1';

// Security
$route['api/security/acces/findByUser/(:any)'] = 'security/AccesUserController/findByUser/$1';
$route['api/security/acces/update/(:any)/(:any)'] = 'security/AccesUserController/update/$1/$2';

$route['api/security/acces-perfil/findByPerfil/(:any)'] = 'security/AccesPerfilController/findByPerfil/$1';
$route['api/security/acces-perfil/update/(:any)/(:any)'] = 'security/AccesPerfilController/update/$1/$2';

// Mascotas
$route['api/config/pet/findActive'] = 'configurations/PetController/findActive';
$route['api/config/pet/findAll'] = 'configurations/PetController/findAll';
$route['api/config/pet/create'] = 'configurations/PetController/create';
$route['api/config/pet/update/(:any)'] = 'configurations/PetController/update/$1';
$route['api/config/pet/delete/(:any)'] = 'configurations/PetController/delete/$1';
$route['api/config/pet/activate/(:any)'] = 'configurations/PetController/activate/$1';
// Vacunas
$route['api/config/vaccine/findActive'] = 'configurations/VaccineController/findActive';
$route['api/config/vaccine/findAll'] = 'configurations/VaccineController/findAll';
$route['api/config/vaccine/create'] = 'configurations/VaccineController/create';
$route['api/config/vaccine/update/(:any)'] = 'configurations/VaccineController/update/$1';
$route['api/config/vaccine/delete/(:any)'] = 'configurations/VaccineController/delete/$1';
$route['api/config/vaccine/activate/(:any)'] = 'configurations/VaccineController/activate/$1';

//razas
$route['api/config/breed/findActive'] = 'configurations/BreedController/findActive';
$route['api/config/breed/findAll'] = 'configurations/BreedController/findAll';
$route['api/config/breed/create'] = 'configurations/BreedController/create';
$route['api/config/breed/update/(:any)'] = 'configurations/BreedController/update/$1';
$route['api/config/breed/delete/(:any)'] = 'configurations/BreedController/delete/$1';
$route['api/config/breed/activate/(:any)'] = 'configurations/BreedController/activate/$1';
// metodo pago
$route['api/config/payment-method/findActive'] = 'configurations/PaymentMethodController/findActive';
$route['api/config/payment-method/findAll'] = 'configurations/PaymentMethodController/findAll';
$route['api/config/payment-method/create'] = 'configurations/PaymentMethodController/create';
$route['api/config/payment-method/update/(:any)'] = 'configurations/PaymentMethodController/update/$1';
$route['api/config/payment-method/delete/(:any)'] = 'configurations/PaymentMethodController/delete/$1';
$route['api/config/payment-method/activate/(:any)'] = 'configurations/PaymentMethodController/activate/$1';
// Servicio
$route['api/config/service/findActive'] = 'configurations/ServiceController/findActive';
$route['api/config/service/findAll'] = 'configurations/ServiceController/findAll';
$route['api/config/service/create'] = 'configurations/ServiceController/create';
$route['api/config/service/update/(:any)'] = 'configurations/ServiceController/update/$1';
$route['api/config/service/delete/(:any)'] = 'configurations/ServiceController/delete/$1';
$route['api/config/service/activate/(:any)'] = 'configurations/ServiceController/activate/$1';
// Servicio
$route['api/config/service-type/findActive'] = 'configurations/ServiceTypeController/findActive';
$route['api/config/service-type/findAll'] = 'configurations/ServiceTypeController/findAll';
$route['api/config/service-type/create'] = 'configurations/ServiceTypeController/create';
$route['api/config/service-type/update/(:any)'] = 'configurations/ServiceTypeController/update/$1';
$route['api/config/service-type/delete/(:any)'] = 'configurations/ServiceTypeController/delete/$1';
$route['api/config/service-type/activate/(:any)'] = 'configurations/ServiceTypeController/activate/$1';
// caja
$route['api/caja/findActive'] = 'caja/CajaController/findActive';
$route['api/caja/findAll'] = 'caja/CajaController/findAll';
$route['api/caja/create'] = 'caja/CajaController/create';
$route['api/caja/update/(:any)'] = 'caja/CajaController/update/$1';
$route['api/caja/delete/(:any)'] = 'caja/CajaController/delete/$1';
$route['api/caja/activate/(:any)'] = 'caja/CajaController/activate/$1';
// caja
$route['api/caja-movi/findFilter'] = 'caja/BoxMovementController/findFilter';
$route['api/caja-movi/findAll'] = 'caja/BoxMovementController/findAll';
$route['api/caja-movi/create'] = 'caja/BoxMovementController/create';
$route['api/caja-movi/update/(:any)'] = 'caja/BoxMovementController/update/$1';
$route['api/caja-movi/delete/(:any)'] = 'caja/BoxMovementController/delete/$1';
$route['api/caja-movi/activate/(:any)'] = 'caja/BoxMovementController/activate/$1';
// 
$route['api/daycare/findPetByClient/(:any)'] = 'DaycareController/findPetByClient/$1';
$route['api/daycare/getDaycare'] = 'DaycareController/getDaycare';
$route['api/daycare/getDataRequerid'] = 'DaycareController/getDataRequerid';
$route['api/daycare/registerIngreso'] = 'DaycareController/registerIngreso';
$route['api/daycare/getDaycareById/(:any)'] = 'DaycareController/getDaycareById/$1';
$route['api/daycare/getDaycareById2/(:any)'] = 'DaycareController/getDaycareById2/$1';
$route['api/daycare/registerSalida'] = 'DaycareController/registerSalida';

$route['api/config/company/findActive'] = 'configurations/CompanyController/findActive';
$route['api/config/company/findAll'] = 'configurations/CompanyController/findAll';
$route['api/config/company/create'] = 'configurations/CompanyController/create';
$route['api/config/company/update/(:any)'] = 'configurations/CompanyController/update/$1';
$route['api/config/company/delete/(:any)'] = 'configurations/CompanyController/delete/$1';

//Impresion
$route['api/impresion/imprimirMovimientoCaja/(:any)'] = 'Impresion/imprimirMovimientoCaja/$1';
$route['api/impresion/imprimirAperturaTurno/(:any)'] = 'Impresion/imprimirAperturaTurno/$1';
$route['api/impresion/imprimirCierreTurno/(:any)'] = 'Impresion/imprimirCierreTurno/$1';
$route['api/config/company/activate/(:any)'] = 'configurations/CompanyController/activate/$1';

//dashboard
$route['api/dashboard/arrivals-departures'] = 'dashboard/DashboardController/getArrivalsDepartures';
$route['api/dashboard/occupation'] = 'dashboard/DashboardController/getOccupation';
$route['api/dashboard/total_clientes'] = 'dashboard/DashboardController/getTotalClientes';
$route['api/dashboard/total_mascotas_estancia'] = 'dashboard/DashboardController/getMascotasEstancia';
$route['api/dashboard/total_ingresos_diarios'] = 'dashboard/DashboardController/getIngresosDiarios';
