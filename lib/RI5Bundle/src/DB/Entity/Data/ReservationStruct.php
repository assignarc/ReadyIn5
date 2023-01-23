<?php

namespace RI5\DB\Entity\Data;

use RI5\DB\Entity\Customer;
use RI5\DB\Entity\Place;
use RI5\DB\Entity\Reservation;

class ReservationStruct
{
    public $status = "SUCCESS";
    public $message;
    public $customer;
    public $place;
    public $reservation;
    public $data;

    
    public function getCustomer(): ?Customer
    {
        if($this->customer)
            return $this->customer;

        $customer = new Customer();
        return $this->customer;
    }
    public function getStatus(): ?string
    {
        if($this->status)
            return $this->status;
        $this->status = "";
        return $this->status;

    }
    public function getMessage(): ?string
    {
        if($this->message)
            return $this->message;

        $this->message="";
        return $this->message;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }
    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }
    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }
    public function getPlace(): ?Place
    {
        if($this->place)
            return $this->place;
        
        $this->place = new Place();
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;
        return $this;
    }
    public function getReservation(): ?Reservation
    {
        if($this->reservation)
            return $this->reservation;

        $this->reservation = new Reservation();
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): self
    {
        $this->reservation = $reservation;
        return $this;
    }
}
