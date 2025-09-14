<?php

namespace RI5\Services;

use RI5\DB\Entity\Customer;
use RI5\Exception\CustomerNotFoundException;
use RI5\Services\BaseService;
use RI5\DB\Repository\CustomerRepository;
use RI5\Services\Traits\EntityAwareTrait;

class CustomerService extends BaseService
{
    use EntityAwareTrait;

    public function __construct(CustomerRepository $customerRepository) 
    {
        $this->objectRepository = $customerRepository;
    }
    
    public function findCustomer(string $phoneNumber): ?Customer {
        
        $customer=  $this->objectRepository->findOneByPhone($phoneNumber);
        return $customer;
    }
    public function findCustomerByUserId(string $userId): ?Customer {
        $customer=  $this->objectRepository->findOneByUserid($userId);
        return $customer;
     }
    public function setContactMethod(string $phone, string $contactMethod){
        $customerDB = $this->findCustomer($phone);

        if(!$customerDB)
           throw new CustomerNotFoundException("Customer with phone {$phone} not found!",9351,[],null   );
           
        $customerDB->setContactMethod($contactMethod);
        $this->objectRepository->save($customerDB,true);
    }
    public function createUpdateCustomer(Customer $customer): Customer {
        $customerDB = $this->findCustomer($customer->getPhone());

        if(!$customerDB){
            $customerDB = new Customer();
            $customerDB->setPhone($customer->getPhone());
         }

        $customerDB->setFirstname($customer->getFirstname() ?? $customerDB->getFirstname());
        $customerDB->setLastname($customer->getLastname() ?? $customerDB->getLastname());

        $customerDB->setAddressline1($customer->getAddressline1() ?? $customerDB->getAddressline1());
        $customerDB->setAddressline2($customer->getAddressline2() ?? $customerDB->getAddressline2());
        $customerDB->setAddressline3($customer->getAddressline3() ?? $customerDB->getAddressline3());
        $customerDB->setCity($customer->getCity() ?? $customerDB->getCity());
        $customerDB->setState($customer->getState() ?? $customerDB->getState());
        $customerDB->setCountry($customer->getCountry() ?? $customerDB->getCountry());
        
        $this->objectRepository->save($customerDB,true);
        
        return $this->findCustomer($customerDB->getPhone());
    }
    public function buildCustomerJSON($cJson):Customer{
        $customer=new Customer();
        $customer->setUserid($cJson->userid ?? 0);
        $customer->setPhone($cJson->phone ?? null);
        $customer->setFirstname($cJson->firstname ?? null);
        $customer->setLastname($cJson->lastname ?? null);

        return $customer;
    }

    public function buildCustomer(string $userid, string $phone, string $firstname, string $lastname): Customer{
        $customer=new Customer();
        $customer->setPhone($phone ?? null);
        $customer->setFirstname($firstname ?? null);
        $customer->setLastname($lastname ?? null);
        return $customer;
    }
    /**
     * Finds all Customers
     */
    public function findAll() {
        $data = $this->objectRepository->findAll();
        return $data;
    }
}