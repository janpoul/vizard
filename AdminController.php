<?php

namespace LPS;

use App\Configs\UserConfig;

abstract class AdminController extends WebController
{

    const DEFAULT_ACCESS = UserConfig::ACCESS_ADMIN_MODULE;

    /**
     * Проверка прав
     * @param string $action
     * @return boolean
     */
    public function isPermission($action)
    {
        return $this->account instanceof \LPS\Models\AuthenticationManagement\Account\Admin;
    }

    /**
     * инициализация родительского модуля
     * метод вызывается только для определения глобальных переменных в не конечных в цепочке иерархии классах
     */
    protected function globalInit()
    {
        $this->segment = \App\Segment::getInstance()->getDefault();
    }

}
