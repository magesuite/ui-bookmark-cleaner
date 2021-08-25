<?php

/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Authorization\Model\Role $role */
$role = $objectManager->get(\Magento\Authorization\Model\RoleFactory::class)->create();
$role->setName('test_custom_role');
$role->setData('role_name', $role->getName());
$role->setRoleType(\Magento\Authorization\Model\Acl\Role\Group::ROLE_TYPE);
$role->setUserType((string)\Magento\Authorization\Model\UserContextInterface::USER_TYPE_ADMIN);

/** @var \Magento\Authorization\Model\ResourceModel\Role $roleResource */
$roleResource = $objectManager->get(\Magento\Authorization\Model\ResourceModel\Role::class);
$roleResource->save($role);

/** @var \Magento\Authorization\Model\ResourceModel\Rules $rules */
$rules = $objectManager->get(\Magento\Authorization\Model\RulesFactory::class)->create();
$rules->setRoleId($role->getId());
//Granted all permissions.
$rules->setResources([$objectManager->get(\Magento\Framework\Acl\RootResource::class)->getId()]);

/** @var \Magento\Authorization\Model\ResourceModel\Rules $rulesResource */
$rulesResource = $objectManager->get(\Magento\Authorization\Model\ResourceModel\Rules::class);
$rulesResource->saveRel($rules);

$adminUsersData = [
    [
        'firstname' => 'John',
        'lastname' => 'Doe',
        'username' => 'TestAdmin1',
        'email' => 'testadmin1@gmail.com',
        'active' => 1,
        'role_id' => $role->getId()
    ],
    [
        'firstname' => 'Jane',
        'lastname' => 'Doe',
        'username' => 'TestAdmin2',
        'email' => 'testadmin2@gmail.com',
        'active' => 1,
        'role_id' => $role->getId()
    ]
];

foreach ($adminUsersData as $adminUserData) {
    /** @var \Magento\User\Model\User $user */
    $user = $objectManager->create(\Magento\User\Model\User::class);
    $user->setFirstname($adminUserData['firstname'])
        ->setLastname($adminUserData['lastname'])
        ->setUsername($adminUserData['username'])
        ->setPassword(\Magento\TestFramework\Bootstrap::ADMIN_PASSWORD)
        ->setEmail($adminUserData['email'])
        ->setIsActive($adminUserData['active'])
        ->setRoleId($adminUserData['role_id']);

    /** @var \Magento\User\Model\ResourceModel\User $userResource */
    $userResource = $objectManager->get(\Magento\User\Model\ResourceModel\User::class);
    $userResource->save($user);
}
