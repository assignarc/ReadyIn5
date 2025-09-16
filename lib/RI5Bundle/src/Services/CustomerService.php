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

        $customerDB->setFirstname($customer->getFirstname() ?? $customerDB->getFirstname())
                ->setLastname($customer->getLastname() ?? $customerDB->getLastname())
                ->setAddressline1($customer->getAddressline1() ?? $customerDB->getAddressline1())
                ->setAddressline2($customer->getAddressline2() ?? $customerDB->getAddressline2())
                ->setAddressline3($customer->getAddressline3() ?? $customerDB->getAddressline3())
                ->setCity($customer->getCity() ?? $customerDB->getCity())
                ->setState($customer->getState() ?? $customerDB->getState())
                ->setCountry($customer->getCountry() ?? $customerDB->getCountry());
        
        $this->objectRepository->save($customerDB,true);
        
        return $this->findCustomer($customerDB->getPhone());
    }
    public function buildCustomerJSON($cJson):Customer{
        $customer=new Customer()
                ->setUserid($cJson->userid ?? 0)
                ->setPhone($cJson->phone ?? null)
                ->setFirstname($cJson->firstname ?? null)
                ->setLastname($cJson->lastname ?? null);

        return $customer;
    }

    public function buildCustomer(string $userid, string $phone, string $firstname, string $lastname): Customer{
        $customer=new Customer()
                ->setPhone($phone ?? null)
                ->setFirstname($firstname ?? null)
                ->setLastname($lastname ?? null);
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