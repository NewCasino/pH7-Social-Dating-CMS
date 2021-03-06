<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Video / Config
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Router\Uri, PH7\Framework\Url\Header;

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        if(!UserCore::auth() && ($this->registry->action === 'addalbum' || $this->registry->action === 'addvideo'
        || $this->registry->action === 'editalbum' || $this->registry->action === 'editvideo'
        || $this->registry->action === 'deletevideo' || $this->registry->action === 'deletealbum'))
        {
            $this->signInRedirect();
        }

        if (!AdminCore::auth() || UserCore::isAdminLoggedAs()) // If the admin is not logged (but can be if the admin use "login as user" feature)
        {
            if (!$this->checkMembership() || !$this->group->view_videos)
            {
                $this->paymentRedirect();
            }
            elseif (($this->registry->action === 'addalbum' || $this->registry->action === 'addvideo') && !$this->group->upload_videos)
            {
                $this->paymentRedirect();
            }

            if ($this->registry->controller === 'AdminController')
            {
                // For security reasons, we do not redirectionnons the user to hide the url of the administrative part.
                Header::redirect(Uri::get('user','main','login'), $this->adminSignInMsg(), 'error');
            }
        }
    }
}
