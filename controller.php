<?php
namespace Concrete\Package\CommunityStoreAuthorizeNetAcceptHosted;

use Concrete\Core\Package\Package;
use Whoops\Exception\ErrorException;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Payment\Method as PaymentMethod;

class Controller extends Package
{
    protected $pkgHandle = 'community_store_authorize_net_accept_hosted';
    protected $appVersionRequired = '8.0';
    protected $pkgVersion = '1.0';


    protected $pkgAutoloaderRegistries = [
        'src/CommunityStore' => '\Concrete\Package\CommunityStoreAuthorizeNetAcceptHosted\Src\CommunityStore',
    ];

    public function getPackageDescription()
    {
        return t("Authorize.Net (Accept Hosted) Payment Method for Community Store");
    }

    public function getPackageName()
    {
        return t("Authorize.Net (Accept Hosted) Payment Method");
    }

    public function install()
    {
        $installed = app()->make('Concrete\Core\Package\PackageService')->getInstalledHandles();
        if(!(is_array($installed) && in_array('community_store',$installed)) ) {
            throw new ErrorException(t('This package requires that Community Store be installed'));
        } else {
            $pkg = parent::install();
            $pm = new PaymentMethod();
            $pm->add('community_store_authorize_net_accept_hosted','Authorize.Net Accept Hosted',$pkg);
        }
    }

    public function on_start() {

    }

    public function uninstall()
    {
        $pm = PaymentMethod::getByHandle('community_store_authorize_net_accept_hosted');
        if ($pm) {
            $pm->delete();
        }
        parent::uninstall();
    }

}
?>
