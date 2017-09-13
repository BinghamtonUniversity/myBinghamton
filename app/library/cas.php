<?php 
//namespace Xavrsl\Cas;
// use Illuminate\Auth\AuthManager;
// use Illuminate\Session\SessionManager;
// use phpCAS;

/**
 * CAS authenticator
 *
 * @package Xavrsl
 * @author Xavier Roussel
 */
class Cas {

	/**
	 * Cas Config
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * Current CAS user
	 *
	 * @var string
	 */
	protected $remoteUser;
	private $isAuthenticated;

	/**
	 * @param $config
	 * @param Auth $auth
	 * @param Session $session
	 */
	public function __construct($path)
	{
		$this->config = Config::get('auth');
		$this->cas_init($path);
	}

	/**
	 * Authenticates the user based on the current request.
	 *
	 * If authentication is successful, true must be returned.
	 * If authentication fails, an exception must be thrown.
	 *
	 * @return bool
	 */
	public function authenticate()
	{
		// attempt to authenticate with CAS server
		if (phpCAS::forceAuthentication()) {
			// retrieve authenticated credentials
			$this->remoteUser = phpCAS::getUser();
			return true;
		} else return false;
	}

	/**
	 * Checks to see is user is authenticated
	 *
	 * @return bool
	 */
	public function isAuthenticated(){
		return $this->isAuthenticated;
	}

	/**
	 * getCurrentUser Alias
	 *
	 * @return array|null
	 */
	public function user(){
		return $this->remoteUser;
	}

	/**
	 * Make PHPCAS Initialization
	 *
	 * Initialize a PHPCAS token request
	 *
	 * @return none
	 */
	private function cas_init($path) {
		// initialize CAS client
		//change to CAS_VERSION_3
		phpCAS::client(SAML_VERSION_1_1, $this->config['cas_hostname'], $this->config['cas_port'], $this->config['cas_uri'], false);
		phpCAS::setNoCasServerValidation();
		if($path !== 'logout' && $path !== '/') {
			phpCAS::setFixedServiceURL(Config::get('app.PRIMARY_DOMAIN_LOCATION').'/'.$path);
		}else{
			phpCAS::setFixedServiceURL(Config::get('app.PRIMARY_DOMAIN_LOCATION'));
		}
		
		$this->isAuthenticated = phpCAS::isAuthenticated();

		phpCAS::setServerSamlValidateURL($this->config['cas_login_url']);
		phpCAS::setServerLogoutURL($this->config['cas_logout_url']);

	}
}
