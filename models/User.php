<?php 

namespace id2Auth\models;

use Yii;
use yii\base\Model;
use yii\web\IdentityInterface;
use id2Auth\Module;

class User extends Model implements IdentityInterface
{

    /**
     * Типы доступов
     */
    const ACCESS_TYPE_EMAG = 1; // доступ к электронному журалу
    const ACCESS_TYPE_MAG = 2; // доступ к печатному журналу
    const ACCESS_TYPE_DEMO = 3; // демодоступ

    /**
     * типов пользователей от А-пресс
     */
    const MEMBER_NULL = 'member_null';
    const MEMBER_ACTIVE_TYPE = 'member_active'; // member_active - пользователь с активными подписками
    const MEMBER_NEW_TYPE = 'member_new'; // member_new – от 0 до 3 событий за 90 дней
    const MEMBER_HOT_TYPE = 'member_hot'; // member_hot – от 4 до 6 событий за 90 дней
    const MEMBER_BAD_TYPE = 'member_bad'; // member_bad – от 7 событий за 90 дней

    const MEMBER_ACTIVE_NEW_MONO_TYPE = 'active_new_mono'; // ?
    const MEMBER_ACTIVE_NEW_MULTI_TYPE = 'active_new_multi'; // ?
    const MEMBER_ACTIVE_OLD_MONO_TYPE = 'active_old_mono'; // ?
    const MEMBER_ACTIVE_OLD_MULTI = 'active_old_multi'; // ?
    const MEMBER_IN_WORK = 'member_in_work'; // ?

    const MEMBER_UNTARGETED = 'untargeted'; // ?
    const MEMBER_BAD_PHONE = 'bad_phone'; // ?
    const MEMBER_NO_PHONE = 'no_phone'; // ?
    const MEMBER_POOR = 'poor'; // ?
    const MEMBER_FREELOADER = 'freeloader'; // ?
    const MEMBER_NEGATIVE = 'negative'; // ?
    const MEMBER_COLD = 'cold'; // ?

    protected $_token;
    protected $_profile;
    protected $_fields;
    protected $_accessList;

    public function init()
    {
        parent::init();

        foreach([
            '_profile',
            '_fields',
            '_accessList',
        ] as $item){
            if(is_null($this->{$item})){
                $this->{$item} = new \stdClass;
            }
        }
    }

    public static function findIdentity($id)
    {
        return unserialize($id);
    }

    public static function findIdentityByAccessToken($token, $type = NULL)
    {
        $module = Module::$instance;
        $user = NULL;

        try{
            if(is_object($P = $module->service->GetProfile($requestParams = [
                'appid' => $appId = $module->appId,
                'token' => $token,
                'sig' => md5(md5('appid' . $appId . 'token' . $token . $appId) . $module->secureKey),
            ])->GetProfileResult) AND isset($P->Id)){
                $user = new static;
                $user->profile->id = $P->Id;
                $user->profile->email = $P->Email;
                $user->profile->name = $P->FirstName;
                $user->profile->surname = $P->LastName;
                $user->profile->patronymic = $P->SecondName;

                if(is_object($EP = $module->service->GetExtendedUserProfile([
                    'appid' => $appId = $module->appId,
                    'fields' => $fields = 'post',
                    'token' => $token,
                    'sig' => md5(md5('appid' . $appId . 'fields' . $fields . 'token' . $token . $appId) . $module->secureKey),
                ])->GetExtendedUserProfileResult)){
                    foreach($EP->Fiels as $item){
                        $user->fields->{$item->Key} = $item->Value;
                    }
                }

                if(is_object($A = $module->service->GetAccess($requestParams)->GetAccessResult) AND isset($A->AccessList->Access)){
                    $CD = date("Y-m-dTH:i:s");
                    foreach((count($A->AccessList->Access) > 1 ? (array) $A->AccessList->Access : [$A->AccessList->Access]) as $item){
                        if($item->EndDate > $CD AND (in_array($item->EditionAccessId, $module->editionAccessId) OR empty($module->editionAccessId))){
                            $user->accessList->{$item->EditionAccessId} = $item;
                        }
                    }
                }

                $user->_token = $token;
            }
        }catch(\Exception $ex){
            
        }

        return $user;
    }

    public function getId()
    {
        return serialize($this);
    }

    public function getProfile()
    {
        return $this->_profile;
    }

    public function getFields()
    {
        return $this->_fields;
    }

    public function getAccessList()
    {
        return $this->_accessList;
    }

    public function getHasAccess()
    {
        $AL = (array) $this->accessList;
        return !empty($AL);
    }

    public function getCustomFields()
    {
        return Yii::$app->session->get('id2UserCustomFields');
    }

    public function setCustomFields($data)
    {
        Yii::$app->session->set('id2UserCustomFields', json_decode(json_encode($data)));
    }

    public function getAuthKey()
    {
        return md5($this->id);
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function getMemberType()
    {
        $activeAccessType = $this->lastAccessType;

        if($activeAccessType == static::ACCESS_TYPE_MAG){
            $R = 'pmember'; // Подписчик печатного журнала
        }elseif($activeAccessType == static::ACCESS_TYPE_EMAG){
            $R = 'emember'; // Подписчик электронного журнала
        }else{
            $R = 'member';
        }

        return $R;
    }

    public function getMemberActiveType()
    {
        $apressMapping = [
            static::MEMBER_NULL,
            static::MEMBER_ACTIVE_TYPE,
            static::MEMBER_NEW_TYPE,
            static::MEMBER_HOT_TYPE,
            static::MEMBER_BAD_TYPE,
            static::MEMBER_ACTIVE_NEW_MONO_TYPE,
            static::MEMBER_ACTIVE_NEW_MULTI_TYPE,
            static::MEMBER_ACTIVE_OLD_MONO_TYPE,
            static::MEMBER_ACTIVE_OLD_MULTI,
            static::MEMBER_IN_WORK,
            static::MEMBER_UNTARGETED,
            static::MEMBER_BAD_PHONE,
            static::MEMBER_NO_PHONE,
            static::MEMBER_POOR,
            static::MEMBER_FREELOADER,
            static::MEMBER_NEGATIVE,
            static::MEMBER_COLD,
        ];

        return (is_null($C = $this->customFields) OR !isset($apressMapping[$C->apress])) ? NULL : $apressMapping[$C->apress];
    }

    protected function getLastAccessType()
    {
        $R = NULL;

        foreach($this->accessList as $A){
            if(is_null($R) OR $A->EndDate > $R->EndDate){
                $R = $A;
            }
        }

        if(!is_null($R)){
            $R = $A->TypeId;
        }

        return $R;
    }

}
