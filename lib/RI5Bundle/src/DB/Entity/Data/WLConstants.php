<?php

namespace RI5\DB\Entity\Data;

abstract class WLConstants 
{
    /**
     * Session Values
     */
    public const S_PLACE_PHONE           = "PLACE_PHONE"; //SESSION_PHONE_TOKEN
    public const S_CUST_PHONE            = "CUST_PHONE"; //SESSION_PHONE_TOKEN
            /**
             * Actual Phone number of customer or Place 
             */
    
    public const S_AUTH_TYPE        = "logintype"; //SESSION_AUTH_TYPE
            /**
             * AuthType - Type of Authorization
             */
            public const AUTHTYPE_CUSTOMER  = "customer";
            public const AUTHTYPE_PLACE     = "place";
            //case AUTHTYPE_NONE = "NONE";

    /**
     * Seperate Authorization for Customer and Place 
     */
    public const S_CUST_AUTHORIZED       = "CUST_AUTHORIZED"; //SESSION_AUTH_TOKEN
    public const S_PLACE_AUTHORIZED      = "PLACE_AUTHORIZED"; //SESSION_AUTH_TOKEN
            /**
             * Authorization Value, is the person Authorized
             */
            public const AUTH_AUTHORIZED = "1";
            public const AUTH_UNAUTHORIZED = "0";

    
    public const S_CUST_CONTACT_METHOD   = "method"; //SESSION_CONTACT_METHOD
    public const S_CUST_PLACESLUG        = "CUST_PLACESLUG"; // Place Value for Customers
    public const S_CUST_ROLE             = "CUST_ROLE";
            public const AUTHROLE_CUSTOMER      = "ROLE_CUSTOMER";

    public const S_PLACE_PUBLIC     = "PLACE_PUBLIC";
    public const S_PLACE_SLUG       = "PLACE_SLUG";
    public const S_PLACE_PLACESLUGS = "PLACE_PLACESLUGS"; //SESSION_REST_TOKEN
    public const S_PLACE_ROLE             = "PLACE_ROLE"; //SESSION_REST_ADMIN_ROLE
            /**
             * AuthRole
             */
            public const AUTHROLE_PLACE_OWNER   = "ROLE_OWNER";
            public const AUTHROLE_PLACE_MANAGER = "ROLE_MANAGER";
            //case AUTHROLE_NONE = "NONE";

    //public const S_PLACE_ADMIN      = "SESSION_PLACE_ADMIN"; //SESSION_REST_ADMIN_TOKEN
    public const S_PLACE_QUEUE      = "queueHash"; //SESSION_REST_QUEUE_HASH
    
    public const S_REDIRECT_URL     = "redirectUrl"; //SESSION_REDIRECT_URL

    /**
     * Generic Values 
     */
    public const NONE = "NONE";
   
    /**
     * Entity Type
     */
     public const ENTITY_PLACE = "PLACE";
     public const ENTITY_CUST = "CUSTOMER";
}
