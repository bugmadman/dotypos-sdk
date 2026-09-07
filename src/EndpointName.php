<?php

namespace BMM\DotyposSdk;

enum EndpointName: string
{
    case AccessToken = 'accessToken';
    case GetCustomer = 'getCustomer';
    case GetCustomers = 'getCustomers';
    case CreateCustomers = 'createCustomers';
    case ReplaceCustomer = 'replaceCustomer';
    case DeleteCustomers = 'deleteCustomers';
    case CreateDiscountGroups = 'createDiscountGroups';
    case GetDiscountGroup = 'getDiscountGroup';
    case DeleteDiscountGroup = 'deleteDiscountGroup';
    case GetDiscountGroups = 'getDiscountGroups';
    case ReplaceDiscountGroup = 'replaceDiscountGroup';
    case ReplaceDiscountGroups = 'replaceDiscountGroups';
    case GetOrder = 'getOrder';
    case GetOrders = 'getOrders';
    case GetOrderItem = 'getOrderItem';
    case GetOrderItems = 'getOrderItems';
    case GetReservation = 'getReservation';
    case GetReservations = 'getReservations';
    case CreateReservations = 'createReservations';
    case ReplaceReservation = 'replaceReservation';
    case ReplaceReservations = 'replaceReservations';
    case DeleteReservation = 'deleteReservation';
    case GetTables = 'getTables';
    case GetBranches = 'getBranches';
    case GetWebhooks = 'getWebhooks';
    case GetWarehouses = 'getWarehouses';
    case RegisterWebhooks = 'registerWebhooks';
    case DeleteWebhook = 'deleteWebhook';
}
