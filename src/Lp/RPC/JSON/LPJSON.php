<?php

use \Lp\RPC\Model\User;


/**
 * LPJSON
 *
 * @author Tobias Fonfara
 * @author Carsten D. Decker
 */

require_once(realpath(dirname(__FILE__) . '/../Model/App.php'));
require_once(realpath(dirname(__FILE__) . '/../Model/Customer.php'));
require_once(realpath(dirname(__FILE__) . '/../Model/Device.php'));
require_once(realpath(dirname(__FILE__) . '/../Model/DeviceEnvironment.php'));
require_once(realpath(dirname(__FILE__) . '/../Model/DeviceType.php'));
require_once(realpath(dirname(__FILE__) . '/../Model/DeviceFilter.php'));
require_once(realpath(dirname(__FILE__) . '/../Model/Field.php'));
require_once(realpath(dirname(__FILE__) . '/../Model/FieldSet.php'));
require_once(realpath(dirname(__FILE__) . '/../Model/Message.php'));
require_once(realpath(dirname(__FILE__) . '/../Model/MessageDetails.php'));
require_once(realpath(dirname(__FILE__) . '/../Model/MessageStatus.php'));
require_once(realpath(dirname(__FILE__) . '/../Model/Statistics.php'));

class Lp_RPC_JSON_LPJSON
{

    /**
     * the generate method keyword used in many places
     *
     * @const string
     */
    const GENERATE = 'generate';


    /**
     * @var string $_authKey
     */
    protected $_authKey;

    /**
     * @param string $authKey
     */
    public function __construct($authKey)
    {
        $this->_authKey = $authKey;
    }

    /**
     * @param string $function
     * @param array  $params
     * @param bool   $isArray
     *
     * @return array
     */
    public function prepare($function, $params, $isArray = false)
    {
        if (!$isArray) {
            $params = array($params);
        }

        $call            = array();
        $call['auth']    = array('key' => $this->_authKey);
        $call[$function] = $params;

        return $call;
    }

    /**
     * @param string $function
     * @param array  $params
     * @param bool   $isArray
     *
     * @return array
     */
    public function prepareUserAuthCall($function, $params, $isArray = false)
    {
        if (!$isArray) {
            $params = array($params);
        }

        $call            = array();
        $call['auth']    = array('key' => $params[0]['userAuthKey']);
        $call[$function] = $params;

        return $call;
    }

    /**
     * @param int                   $offset
     * @param int                   $limit
     * @param Lp_RPC_Model_FieldSet $fieldSet
     *
     * @return array
     */
    public function prepareDeviceListCall($offset = 0, $limit = 1000, $fieldSet = null)
    {
        $params           = array();
        $params['offset'] = $offset;
        $params['limit']  = $limit;

        if (!empty($fieldSet)) {
            $params['data'] = $fieldSet->getData();
        } else {
            $params['data'] = array();
        }

        return $this->prepare('list', $params);
    }

    /**
     * @param \Lp_RPC_Model_Device $device
     *
     * @return array
     */
    public function prepareDeviceRegisterCall($device)
    {
        $params                       = array();
        $params['env']                = $device->getEnvironment();
        $params['type']               = $device->getType();
        $params['uid']                = $device->getUid();
        $params['infoHardware']       = $device->getInfoHardware();
        $params['infoSystem']         = $device->getInfoSystem();
        $params['infoSystemLanguage'] = $device->getInfoSystemLanguage();
        $params['infoTimezone']       = $device->getInfoTimezone();
        $params['optedOut']           = $device->getOptedOut();

        return $this->prepare('register', $params);
    }

    /**
     * @param \Lp_RPC_Model_Device $device
     * @param string               $lpUid
     *
     * @return array
     */
    public function prepareConvertOptedOutDeviceCall($device, $lpUid)
    {
        $params                       = array();
        $params['env']                = $device->getEnvironment();
        $params['type']               = $device->getType();
        $params['uid']                = $device->getUid();
        $params['infoHardware']       = $device->getInfoHardware();
        $params['infoSystem']         = $device->getInfoSystem();
        $params['infoSystemLanguage'] = $device->getInfoSystemLanguage();
        $params['infoTimezone']       = $device->getInfoTimezone();
        $params['lpUid']              = $lpUid;

        return $this->prepare('convertOptedOutDevice', $params);
    }

    /**
     * @param \Lp_RPC_Model_Device $device
     * @param string               $oldDeviceUid
     *
     * @return array
     */
    public function prepareOptOutRegisteredDeviceCall($device, $oldDeviceUid)
    {
        $params                       = array();
        $params['env']                = $device->getEnvironment();
        $params['type']               = $device->getType();
        $params['uid']                = $device->getUid();
        $params['infoHardware']       = $device->getInfoHardware();
        $params['infoSystem']         = $device->getInfoSystem();
        $params['infoSystemLanguage'] = $device->getInfoSystemLanguage();
        $params['infoTimezone']       = $device->getInfoTimezone();
        $params['oldDeviceUid']       = $oldDeviceUid;

        return $this->prepare('optOutRegisteredDevice', $params);
    }

    /**
     * @param Lp_RPC_Model_Device $device
     * @param string              $eventType
     *
     * @return array
     */
    public function prepareDeviceRegisterEventCall($device, $eventType)
    {
        $params              = array();
        $params['env']       = $device->getEnvironment();
        $params['type']      = $device->getType();
        $params['uid']       = $device->getUid();
        $params['eventType'] = $eventType;

        return $this->prepare('event', $params);
    }

    /**
     * @param Lp_RPC_Model_Device $device
     * @param array               $data
     *
     * @return array
     */
    public function prepareDeviceSetDataCall($device, $data)
    {
        $params         = array();
        $params['env']  = $device->getEnvironment();
        $params['type'] = $device->getType();
        $params['uid']  = $device->getUid();
        $params['data'] = $data;

        return $this->prepare('setData', $params);
    }

    /**
     * @param Lp_RPC_Model_Device $device
     * @param int                 $offset
     * @param int                 $limit
     *
     * @return array
     */
    public function prepareDeviceMessageInboxCall($device, $offset, $limit)
    {
        $params           = array();
        $params['env']    = $device->getEnvironment();
        $params['type']   = $device->getType();
        $params['uid']    = $device->getUid();
        $params['offset'] = $offset;
        $params['limit']  = $limit;

        return $this->prepare('messageInbox', $params);
    }

    /**
     * @param Lp_RPC_Model_Device $device
     *
     * @return array
     */
    public function prepareDeviceUnregisterCall($device)
    {
        $params         = array();
        $params['env']  = $device->getEnvironment();
        $params['type'] = $device->getType();
        $params['uid']  = $device->getUid();

        return $this->prepare('unregister', $params);
    }

    /**
     * @param Lp_RPC_Model_DeviceFilter $deviceFilter
     *
     * @return array
     */
    public function prepareDeviceFilterCreateCall($deviceFilter)
    {
        $params             = array();
        $params['id']       = $deviceFilter->getId();
        $params['name']     = $deviceFilter->getName();
        $params['criteria'] = $deviceFilter->getCriteria();

        return $this->prepare('create', $params);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function prepareDeviceFilterDeleteCall($id)
    {
        $params       = array();
        $params['id'] = $id;

        return $this->prepare('delete', $params);
    }

    /**
     * @param array $deviceFilters
     *
     * @return array
     */
    public function prepareDeviceFilterCalcCriteriaCall($deviceFilters)
    {
        $params = array();

        foreach ($deviceFilters as $deviceFilter) {
            /** @var Lp_RPC_Model_DeviceFilter $deviceFilter */
            $params[] = array('id' => $deviceFilter->getId());
        }

        return $this->prepare('calcCriteria', $params, true);
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function prepareDeviceFilterListCall($offset = 0, $limit = 100)
    {
        $params           = array();
        $params['offset'] = $offset;
        $params['limit']  = $limit;

        return $this->prepare('list', $params);
    }

    /**
     * @param Lp_RPC_Model_DeviceFilter $deviceFilter
     *
     * @return array
     */
    public function prepareDeviceFilterUpdateCall($deviceFilter)
    {
        $params             = array();
        $params['id']       = $deviceFilter->getId();
        $params['name']     = $deviceFilter->getName();
        $params['criteria'] = $deviceFilter->getCriteria();

        return $this->prepare('update', $params);
    }

    /**
     * @param Lp_RPC_Model_Field $field
     *
     * @return array
     */
    public function prepareFieldCreateCall($field)
    {
        $params                    = array();
        $params['name']            = $field->getName();
        $params['descriptiveName'] = $field->getDescriptiveName();
        $params['tagBased']        = $field->getTagBased();

        return $this->prepare('create', $params);
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function prepareFieldListCall($offset = 0, $limit = 100)
    {
        $params           = array();
        $params['offset'] = $offset;
        $params['limit']  = $limit;

        return $this->prepare('list', $params);
    }

    /**
     * @param array $messageIds
     *
     * @return array
     */
    public function prepareMessageDetailsCall($messageIds)
    {
        $params = array();

        for ($i = 0; $i < count($messageIds); $i++) {
            $params[] = array('id' => $messageIds[$i]);
        }

        return $this->prepare('details', $params, true);
    }

    /**
     * @param \Lp_RPC_Model_MessageDetails $messageDetails
     *
     * @return array
     */
    public function prepareUpdateMessage(\Lp_RPC_Model_MessageDetails $messageDetails)
    {
        $params = array(
            'id'                    => $messageDetails->getId(),
            '_content'              => $messageDetails->getContent(),
            '_data'                 => $messageDetails->getData(),
            '_extendedData'         => $messageDetails->getExtendedData(),
            'pushData'              => $messageDetails->getPushData(),
            '_expiry'               => $messageDetails->getExpiry(),
            '_schedule'             => $messageDetails->getScheduleDate(),
            'inboxOnly'             => $messageDetails->getInboxOnly(),
            'iosExpiry'             => $messageDetails->getIosExpiry(),
            'iosAlert'              => $messageDetails->getIosAlert(),
            'iosBadge'              => $messageDetails->getIosBadge(),
            'iosContentAvailable'   => $messageDetails->getIosContentAvailable(),
            'iosSound'              => $messageDetails->getIosSound(),
            'iosData'               => $messageDetails->getIosData(),
            'androidTimeToLive'     => $messageDetails->getAndroidTimeToLive(),
            'androidDelayWhileIdle' => $messageDetails->getAndroidDelayWhileIdle(),
            'androidCollapseKey'    => $messageDetails->getAndroidCollapseKey(),
            'androidData'           => $messageDetails->getAndroidData()
        );

        return $this->prepare('update', $params, true);
    }

    /**
     * @param int $messageId
     *
     * @return array
     */
    public function prepareDeleteMessage($messageId)
    {
        $params = array(
            'id' => $messageId
        );

        return $this->prepare('delete', $params, true);
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function prepareMessageListCall($offset = 0, $limit = 100)
    {
        $params               = array();
        $params['offset']     = $offset;
        $params['limit']      = $limit;
        $params['statistics'] = true;

        return $this->prepare('list', $params);
    }

    /**
     * @param Lp_RPC_Model_Message                                  $message
     * @param array|string                                          $type
     * @param string                                                $env
     * @param Lp_RPC_Model_FieldSet|Lp_RPC_Model_DeviceFilter|array $fieldSetOrDeviceFilters
     * @param bool                                                  $inboxOnly
     *
     * @return array
     */
    public function preparePushSendCall($message,
        $type = null,
        $env = null,
        $fieldSetOrDeviceFilters = null,
        $inboxOnly = false
    ) {
        $params              = array();
        $params['message']   = $this->buildPushSendMessageCreateArray($message);
        $params['inboxOnly'] = $inboxOnly;

        $groups = $this->buildPushSendGroupArray($type, $env);

        if (!empty($groups)) {
            $params['group'] = $groups;
        }
        if (!empty($fieldSetOrDeviceFilters)) {
            if (!is_array($fieldSetOrDeviceFilters)) {
                $fieldSetOrDeviceFilters = array($fieldSetOrDeviceFilters);
            }
            foreach ($fieldSetOrDeviceFilters as $fieldSetOrDeviceFilter) {
                switch (get_class($fieldSetOrDeviceFilter)) {
                    case 'Lp_RPC_Model_FieldSet' :
                        $params['field'] = $fieldSetOrDeviceFilter->getData();
                        break 2;
                    case 'Lp_RPC_Model_DeviceFilter' :
                        if (empty($params['filter'])) {
                            $params['filter'] = array();
                        }
                        $params['filter'] =
                            array_merge($params['filter'], $fieldSetOrDeviceFilter->getCriteria());
                        break;
                }
            }
        }

        return $this->prepare('send', $params);
    }

    /**
     * @param Lp_RPC_Model_Device         $device
     * @param Lp_RPC_Model_MessageDetails $message
     *
     * @return array
     */
    public function prepareResponseUpdateCall($device, $message)
    {
        $params              = array();
        $params['messageId'] = $message->getId();
        $params['env']       = $device->getEnvironment();
        $params['type']      = $device->getType();
        $params['uid']       = $device->getUid();

        return $this->prepare('update', $params);
    }

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     *
     * @return array
     */
    public function prepareStatisticsStatisticsCall($startDate, $endDate)
    {
        $params              = array();
        $params['startDate'] = $startDate->format('Y-m-d');
        $params['endDate']   = $endDate->format('Y-m-d');

        return $this->prepare('statistics', $params);
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return array
     */
    public function prepareUserLoginCall($email, $password)
    {
        $params             = array();
        $params['email']    = $email;
        $params['password'] = $password;

        return $this->prepare('login', $params);
    }

    /**
     * @param string $email
     *
     * @return array
     */
    public function prepareSetHasAcceptedTC($email)
    {
        $params          = array();
        $params['email'] = $email;

        return $this->prepare('setHasAcceptedTC', $params);
    }

    /**
     * @param \Lp_RPC_Model_Customer $customer
     *
     * @return array
     */
    public function prepareRegisterCustomerCall(\Lp_RPC_Model_Customer $customer)
    {
        $params                           = array();
        $params['email']                  = $customer->getEmail();
        $params['customerName']           = $customer->getName();
        $params['appName']                = $customer->getAppName();
        $params['contactPersonGender']    = $customer->getContactPersonGender();
        $params['contactPersonFirstName'] = $customer->getContactPersonFirstName();
        $params['contactPersonLastName']  = $customer->getContactPersonLastName();
        $params['language']               = $customer->getLanguage();
        $params['password']               = $customer->getPassword();

        return $this->prepare('register', $params);
    }


    /**
     * @param string           $userAuthKey
     * @param Lp_RPC_Model_App $app
     *
     * @return array
     */
    public function prepareCreateApp($userAuthKey, \Lp_RPC_Model_App $app)
    {
        $params                = array();
        $params['userAuthKey'] = $userAuthKey;
        $params['appName']     = $app->getName();
        $params['email']       = $app->getContactEmail();

        return $this->prepareUserAuthCall('create', $params);
    }


    /**
     * @param string $userAuthKey
     * @param User   $user
     *
     * @return array
     */
    public function prepareCreateUser($userAuthKey, User $user)
    {
        $params                = array();
        $params['userAuthKey'] = $userAuthKey;

        $params['email']          = $user->getEmail();
        $params['password']       = $user->getPassword();
        $params['name']           = $user->getName();
        $params['isAccountAdmin'] = $user->getIsAccountAdmin();

        return $this->prepareUserAuthCall('create', $params);
    }


    /**
     * @param string $userAuthKey
     * @param User   $user
     *
     * @return array
     */
    public function prepareUpdateUser($userAuthKey, User $user)
    {
        $params                = array();
        $params['userAuthKey'] = $userAuthKey;

        $params['id']             = $user->getId();
        $params['email']          = $user->getEmail();
        $params['password']       = $user->getPassword();
        $params['name']           = $user->getName();
        $params['isAccountAdmin'] = $user->getIsAccountAdmin();

        return $this->prepareUserAuthCall('update', $params);
    }


    /**
     * @param string   $userAuthKey
     * @param password $email
     * @param password $password
     *
     * @return array
     */
    public function prepareSendWelcomeEmail($userAuthKey, $email, $password)
    {
        $params = array(
            'userAuthKey' => $userAuthKey,
            'email'       => $email,
            'password'    => $password
        );

        return $this->prepareUserAuthCall('sendWelcomeEmail', $params);
    }


    /**
     * @param string   $userAuthKey
     * @param password $password
     *
     * @return array
     */
    public function prepareUpdateOwnPassword($userAuthKey, $password)
    {
        $params = array(
            'userAuthKey' => $userAuthKey,
            'password'    => $password
        );

        return $this->prepareUserAuthCall('updateOwnPassword', $params);
    }


    /**
     * @param string $userAuthKey
     * @param int    $userId
     *
     * @return array
     */
    public function prepareDeleteUser($userAuthKey, $userId)
    {
        $params                = array();
        $params['userAuthKey'] = $userAuthKey;
        $params['userId']      = $userId;

        return $this->prepareUserAuthCall('delete', $params);
    }

    /**
     * @param string $androidAuthKey
     *
     * @return array
     */
    public function prepareUpdateAppAndroidAuthKey($androidAuthKey)
    {
        $params                   = array();
        $params['androidAuthKey'] = $androidAuthKey;

        return $this->prepare('updateAppAndroidAuthKey', $params);
    }

    /**
     * @param string $iOSCertFileContentsBase64
     * @param string $deviceEnv
     * @param string $passphrase
     *
     * @return array
     */
    public function prepareUpdateAppIOSCert($iOSCertFileContentsBase64,
        $deviceEnv,
        $passphrase = ''
    ) {
        $params                              = array();
        $params['iOSCertFileContentsBase64'] = $iOSCertFileContentsBase64;
        $params['deviceEnv']                 = $deviceEnv;
        $params['passphrase']                = $passphrase;

        return $this->prepare('updateAppIOSCert', $params);
    }

    /**
     * @return array
     */
    public function prepareGetAppIOSCertsDetails()
    {
        $params = array();

        return $this->prepare('getAppIOSCertsDetails', $params);
    }

    /**
     * @param string $tokenType
     *
     * @return array
     */
    public function prepareGenerateTokenCall($tokenType)
    {
        $params              = array();
        $params['tokenType'] = $tokenType;

        return $this->prepare('generate', $params);
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function prepareImportListCall($offset = 0, $limit = 100)
    {
        $params           = array();
        $params['offset'] = $offset;
        $params['limit']  = $limit;

        return $this->prepare('list', $params);
    }


    /**
     * @param array $object
     *
     * @return array array
     */
    public static function parseDeviceListResult($object)
    {
        $list = array();
        if (array_key_exists('result', $object)) {
            if (array_key_exists('list', $object['result'])) {
                $devices = $object['result']['list'][0];
                foreach ($devices as $value) {
                    if (is_array($value)) {
                        $uid         = $value['deviceUid'];
                        $type        = Lp_RPC_Model_DeviceType::getDeviceType($value['deviceType']);
                        $environment =
                            Lp_RPC_Model_DeviceEnvironment::getDeviceEnvironment($value['deviceEnv']
                            );
                        $fieldSet    = new Lp_RPC_Model_FieldSet();
                        $data        = $value['data'];
                        foreach ($data as $fieldName => $fieldValue) {
                            $fieldSet->addField($fieldName, ((string) $fieldValue));
                        }
                        $list[] = new Lp_RPC_Model_Device($uid, $type, $environment, $fieldSet);
                    }
                }
            }
        }

        return $list;
    }

    /**
     * @param array $object
     *
     * @return array
     */
    public static function parseDeviceFilterListResult($object)
    {
        $deviceFilters = array();
        if (array_key_exists('result', $object)) {
            if (array_key_exists('list', $object['result'])) {
                $list = $object['result']['list'];
                foreach ($list as $array) {
                    foreach ($array as $value) {
                        $deviceFilters[] = new Lp_RPC_Model_DeviceFilter($value['id'],
                            $value['name'],
                            $value['criteria']
                        );
                    }
                }
            }
        }

        return $deviceFilters;
    }

    /**
     * @param array $deviceFilters
     * @param array $object
     *
     * @return array
     */
    public static function parseDeviceFilterCalcCriteriaResult($deviceFilters, $object)
    {
        if (array_key_exists('result', $object)) {
            if (array_key_exists('calcCriteria', $object['result'])) {
                $result = $object['result']['calcCriteria'];
                foreach ($result as $calcCriteria) {
                    foreach ($deviceFilters as $deviceFilter) {
                        /** @var Lp_RPC_Model_DeviceFilter $deviceFilter */
                        if ($deviceFilter->getId() == $calcCriteria['id']) {
                            $deviceFilter->setCalcDevicesTotal($calcCriteria['devicesTotal']);
                            break;
                        }
                    }
                }
            }
        }

        return $deviceFilters;
    }

    /**
     * @param array $object
     *
     * @return array
     */
    public static function parseError($object)
    {
        $errors = array();
        if (array_key_exists('error', $object)) {
            $errors[] = $object['error'];
        }
        if (array_key_exists('result', $object)) {
            $result = $object['result'];
            foreach ($result as $array) {
                if (is_array($array)) {
                    foreach ($array as $value) {
                        if (is_array($value) && array_key_exists('errors', $value)) {
                            $errors[] = $value['errors'];
                        }
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * @param array $object
     *
     * @return array
     */
    public static function parseFieldListResult($object)
    {
        $fields = array();
        if (array_key_exists('result', $object)) {
            if (array_key_exists('list', $object['result'])) {
                $list = $object['result']['list'];
                foreach ($list as $array) {
                    foreach ($array as $value) {
                        $descriptiveName =
                            !empty($value['descriptiveName']) ? $value['descriptiveName'] : '';
                        $tagBased        = (bool) ($value['tagBased']);

                        $fields[] =
                            new Lp_RPC_Model_Field($value['name'], $descriptiveName, $tagBased);
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * @param array $object
     *
     * @return array
     */
    public static function parseMessageDetailsResult($object)
    {
        /** @var \Lp_RPC_Model_MessageDetails[] $messages */
        $messages = array();
        if (!array_key_exists('result', $object)
            || !array_key_exists('details', $object['result'])
        ) {
            return $messages;
        }

        $details      = $object['result']['details'];
        $messageIndex = 0;
        foreach ($details as $array) {
            if (array_key_exists('content', $array) && array_key_exists('data', $array)
                && array_key_exists('extendedData',
                    $array
                )
            ) {
                $messages[$messageIndex] = new Lp_RPC_Model_MessageDetails($array['id'],
                    $array['content'],
                    json_decode($array['data'], true),
                    json_decode($array['extendedData'], true)
                );

                $messages[$messageIndex]->setPushData(json_decode($array['pushData'], true));
                $messages[$messageIndex]->setExpiry($array['expiry']);
                $messages[$messageIndex]->setScheduleDate($array['schedule']);
                $messages[$messageIndex]->setInboxOnly($array['inboxOnly']);
                $messages[$messageIndex]->setIosExpiry($array['iosExpiry']);
                $messages[$messageIndex]->setIosAlert($array['iosAlert']);
                $messages[$messageIndex]->setIosBadge($array['iosBadge']);
                $messages[$messageIndex]->setIosContentAvailable($array['iosContentAvailable']);
                $messages[$messageIndex]->setIosSound($array['iosSound']);
                $messages[$messageIndex]->setIosData($array['iosData']);
                $messages[$messageIndex]->setAndroidTimeToLive($array['androidTimeToLive']);
                $messages[$messageIndex]->setAndroidDelayWhileIdle($array['androidDelayWhileIdle']);
                $messages[$messageIndex]->setAndroidCollapseKey($array['androidCollapseKey']);
                $messages[$messageIndex]->setAndroidData($array['androidData']);

                $messageIndex++;
            }
        }

        return $messages;
    }

    /**
     * @param array $object
     *
     * @return array
     */
    public static function parseMessageListResult($object)
    {
        $messages = array();
        if (array_key_exists('result', $object)) {
            if (array_key_exists('list', $object['result'])) {
                $list = $object['result']['list'];
                foreach ($list as $array) {
                    foreach ($array as $value) {
                        $messages[] = new Lp_RPC_Model_MessageStatus($value);
                    }
                }
            }
        }

        return $messages;
    }

    /**
     * @param array $object
     *
     * @return array
     */
    public static function parseStatisticsStatisticsResult($object)
    {
        $statistics = array();
        if (array_key_exists('result', $object)) {
            if (array_key_exists('statistics', $object['result'])) {
                $data = $object['result']['statistics'][0];
                foreach ($data as $array) {
                    if (is_array($array)) {
                        $statistics[] = new Lp_RPC_Model_Statistics($array);
                    }
                }
            }
        }

        return $statistics;
    }

    /**
     * @param array $object
     *
     * @return Lp_RPC_Model_Customer|void
     */
    public static function parseUserLoginResult($object)
    {
        if (!array_key_exists('result', $object)
            || !array_key_exists('login', $object['result'])
        ) {
            return null;
        }

        $login = $object['result']['login'][0];
        if (!is_array($login)) {
            return null;
        }

        $apps = array();
        if (array_key_exists('apps', $login)) {
            foreach ($login['apps'] as $app) {
                if (is_array($app)) {
                    $apps[] =
                        new Lp_RPC_Model_App(((int) $app['id']),
                            $app['name'],
                            $app['authKeyClient'],
                            $app['authKeyServer']
                        );
                }
            }
        }

        $users = array();
        if (array_key_exists('users', $login)) {
            foreach ($login['users'] as $user) {
                if (is_array($user)) {
                    $users[] =

                        // @NB: we don't know the user password at this stage. Only the hash. So use
                        // an empty string.
                        new User(
                            $user['id'],
                            $user['userEmail'],
                            '',
                            $user['name'],
                            $user['isAccountAdmin']
                        );
                }
            }
        }

        $customer = new Lp_RPC_Model_Customer($login['name'], $login['consoleLogo'], $apps);
        $customer->setHasAcceptedTC((bool) $login['hasAcceptedTC']);
        $customer->setAuthKey($login['authKey']);
        $customer->setUsers($users);
        $customer->setIsAccountAdmin($login['isAccountAdmin']);

        return $customer;
    }

    /**
     * @param array $object
     *
     * @return array
     */
    public static function parseImportListResult($object)
    {
        $imports = array();
        if (array_key_exists('result', $object)) {
            if (array_key_exists('list', $object['result'])) {
                $imports = $object['result']['list'][0];
            }
        }

        return $imports;
    }


    /**
     * @param array $object
     *
     * @throws Exception
     *
     * @return string
     */
    public function parseRegisterCustomerResult($object)
    {
        if (array_key_exists('result', $object)) {
            if (isset($object['result']['register'])) {
                $id = $object['result']['register'];

                return $id;
            }
        }

        throw new Exception('Unexpected exception occurred while parsing registerCustomer result', 0
        );
    }


    /**
     * @param array $object
     *
     * @throws Exception
     *
     * @return string
     */
    public function parseGenerateTokenResult($object)
    {
        if (array_key_exists('result', $object)) {
            if (isset($object['result'][self::GENERATE])) {
                $token = $object['result'][self::GENERATE];

                return $token;
            }
        }

        throw new Exception('Unexpected exception occurred while parsing generateToken result', 0);
    }

    /**
     * @param string|array $types
     * @param string       $env
     *
     * @return array
     */
    private function buildPushSendGroupArray($types, $env)
    {
        $array = array();
        if (is_string($types)) {
            $types = array($types);
        }
        if (is_array($types)) {
            foreach ($types as $type) {
                $values = array();
                if (!is_null($type)) {
                    $values['type'] = $type;
                }
                if (!is_null($env)) {
                    $values['env'] = $env;
                }
                if (!empty($values)) {
                    $array[] = $values;
                }
            }
        }

        return $array;
    }

    /**
     * @param Lp_RPC_Model_Message $message
     *
     * @return array
     */
    private static function buildPushSendMessageArray($message)
    {
        $array             = array();
        $array['_content'] = $message->getContent();

        if (!is_null($message->getExpiryDate())) {
            $array['_expiry'] = $message->getExpiryDate();
        }
        if (!is_null($message->getScheduleDate())) {
            $array['_schedule'] = $message->getScheduleDate();
        }
        if (!is_null($message->getData())) {
            $array['_data'] = $message->getData();
        }
        if (!is_null($message->getIosExpiryDate())) {
            $array['iosExpiryDate'] = $message->getIosExpiryDate();
        }
        if (!is_null($message->getIosAlert())) {
            $array['iosAlert'] = $message->getIosAlert();
        }
        if (!is_null($message->getIosBadge())) {
            $array['iosBadge'] = $message->getIosBadge();
        }
        if (!is_null($message->getIosContentAvailable())) {
            $array['iosContentAvailable'] = $message->getIosContentAvailable();
        }
        if (!is_null($message->getIosSound())) {
            $array['iosSound'] = $message->getIosSound();
        }
        if (!is_null($message->getIosData())) {
            $array['iosData'] = $message->getIosData();
        }
        if (!is_null($message->getAndroidTimeToLive())) {
            $array['androidTimeToLive'] = $message->getAndroidTimeToLive();
        }
        if (!is_null($message->getAndroidDelayWhileIdle())) {
            $array['androidDelayWhileIdle'] = $message->getAndroidDelayWhileIdle();
        }
        if (!is_null($message->getAndroidCollapseKey())) {
            $array['androidCollapseKey'] = $message->getAndroidCollapseKey();
        }
        if (!is_null($message->getAndroidData())) {
            $array['androidData'] = $message->getAndroidData();
        }
        if (!is_null($message->getWpToastTitle())) {
            $array['wpToastTitle'] = $message->getWpToastTitle();
        }
        if (!is_null($message->getWpToastSubtitle())) {
            $array['wpToastSubtitle'] = $message->getWpToastSubtitle();
        }
        if (!is_null($message->getWpToastPath())) {
            $array['wpToastPath'] = $message->getWpToastPath();
        }
        if (!is_null($message->getWpToastData())) {
            $array['wpToastData'] = $message->getWpToastData();
        }
        if (!is_null($message->getWpTileTitle())) {
            $array['wpTileTitle'] = $message->getWpTileTitle();
        }
        if (!is_null($message->getWpTileCount())) {
            $array['wpTileCount'] = $message->getWpTileCount();
        }
        if (!is_null($message->getWpTileBackgroundImage())) {
            $array['wpTileBackgroundImage'] = $message->getWpTileBackgroundImage();
        }
        if (!is_null($message->getWpTileBackTitle())) {
            $array['wpTileBackTitle'] = $message->getWpTileBackTitle();
        }
        if (!is_null($message->getWpTileBackContent())) {
            $array['wpTileBackContent'] = $message->getWpTileCount();
        }
        if (!is_null($message->getWpTileBackBackgroundImage())) {
            $array['wpTileBackBackgroundImage'] = $message->getWpTileBackBackgroundImage();
        }

        return $array;
    }

    /**
     * @param Lp_RPC_Model_Message $message
     *
     * @return array
     */
    private static function buildPushSendMessageCreateArray($message)
    {
        $array           = array();
        $array['create'] = self::buildPushSendMessageArray($message);

        return $array;
    }
}
