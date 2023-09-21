<?php


namespace Module\Member\Provider\Auth;


use Module\Vendor\Provider\ProviderTrait;

/**
 * @method static AbstractMemberAuthProvider[] listAll()
 */
class MemberAuthProvider
{
    use ProviderTrait;

    /**
     * 调用方法
     * @param $method string onWebLogin|onWebLogout
     * @param $param array
     * @return null|array
     */
    public static function call($method, $param = [])
    {
        $providers = MemberAuthProvider::listAll();
        foreach ($providers as $provider) {
            if (!$provider->enabled()) {
                continue;
            }
            $result = call_user_func_array([$provider, $method], [$param]);
            if (null !== $result) {
                return $result;
            }
        }
        return null;
    }
}
