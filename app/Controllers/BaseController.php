<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['setting'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        // Get sidebar color from settings
        $db = \Config\Database::connect();
        $role = session()->get('role');
        $sidebarColor = $db->table('settings')
            ->where('key', 'sidebar_color_' . $role)
            ->get()
            ->getRowArray();

        // Set default colors if not set
        $defaultColors = [
            'admin' => 'linear-gradient(to bottom, #4e73df, #224abe)',
            'guru' => 'linear-gradient(to bottom, #1cc88a, #169b6b)',
            'siswa' => 'linear-gradient(to bottom, #f6c23e, #dda20a)'
        ];

        $sidebarColorValue = $sidebarColor ? $sidebarColor['value'] : ($defaultColors[$role] ?? $defaultColors['admin']);
        session()->set([
            'sidebar_color_' . $role => $sidebarColorValue
        ]);
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }
}
