<?php
	/**
	 * Установщик модуля
	 */

	/**
	 * @var array $INFO реестр модуля
	 */
	$INFO = Array();
	$INFO['name'] = "emarket";
	$INFO['config'] = "1";
	$INFO['ico'] = "ico_eshop";
	$INFO['default_method_admin'] = "orders";
	$INFO['enable-discounts'] = "1";
	$INFO['enable-currency'] = "1";
	$INFO['enable-stores'] = "1";
	$INFO['enable-payment'] = "1";
	$INFO['enable-delivery'] = "1";
	$INFO['delivery-with-address'] = "0";
	$INFO['purchasing-one-step'] = "0";

	/**
	 * @var array $COMPONENTS файлы модуля
	 */
	$COMPONENTS = array();
	$COMPONENTS[] = "./classes/components/emarket/admin.php";
	$COMPONENTS[] = "./classes/components/emarket/autoload.php";
	$COMPONENTS[] = "./classes/components/emarket/class.php";
	$COMPONENTS[] = "./classes/components/emarket/customAdmin.php";
	$COMPONENTS[] = "./classes/components/emarket/customMacros.php";
	$COMPONENTS[] = "./classes/components/emarket/events.php";
	$COMPONENTS[] = "./classes/components/emarket/handlers.php";
	$COMPONENTS[] = "./classes/components/emarket/i18n.en.php";
	$COMPONENTS[] = "./classes/components/emarket/i18n.php";
	$COMPONENTS[] = "./classes/components/emarket/includes.php";
	$COMPONENTS[] = "./classes/components/emarket/install.php";
	$COMPONENTS[] = "./classes/components/emarket/lang.en.php";
	$COMPONENTS[] = "./classes/components/emarket/lang.php";
	$COMPONENTS[] = "./classes/components/emarket/macros.php";
	$COMPONENTS[] = "./classes/components/emarket/notification.php";
	$COMPONENTS[] = "./classes/components/emarket/permissions.php";
	$COMPONENTS[] = "./classes/components/emarket/printInvoice.php";
	$COMPONENTS[] = "./classes/components/emarket/purchasingOneClick.php";
	$COMPONENTS[] = "./classes/components/emarket/purchasingOneStep.php";
	$COMPONENTS[] = "./classes/components/emarket/purchasingStages.php";
	$COMPONENTS[] = "./classes/components/emarket/purchasingStagesSteps.php";
	$COMPONENTS[] = "./classes/components/emarket/statReports.php";
	$COMPONENTS[] = "./classes/components/emarket/umiManagerAPI.php";
	$COMPONENTS[] = "./classes/components/emarket/yandexMarketClient.php";
	$COMPONENTS[] = "./classes/components/emarket/deliverySettingsAdmin.php";
	$COMPONENTS[] = "./classes/components/emarket/services.php";
	$COMPONENTS[] = "./classes/components/emarket/settings.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/customer/customer.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/discount.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/discountModificator.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/discountRule.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/iItemDiscountRule.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/iOrderDiscountRule.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/discounts/bonusDiscount.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/discounts/itemDiscount.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/discounts/orderDiscount.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/modificators/absolute.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/modificators/proc.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/rules/allOrdersPrices.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/rules/dataRange.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/rules/items.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/rules/orderPrice.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/rules/relatedItems.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/rules/userGroups.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/discounts/rules/users.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/orders/order.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/orders/Calculator.class.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/orders/orderItem.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/orders/items/custom.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/orders/items/digital.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/orders/items/optioned.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/orders/number/default.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/orders/number/iOrderNumber.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/payment/payment.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/payment/api/kupivkredit.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/payment/systems/acquiropay.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/payment/systems/courier.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/payment/systems/dengionline.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/payment/systems/invoice.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/payment/systems/kupivkredit.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/payment/systems/payanyway.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/payment/systems/payonline.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/payment/systems/paypal.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/payment/systems/rbk.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/payment/systems/receipt.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/payment/systems/robox.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/payment/systems/yandex30.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/stat/emarketTop.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/Address/Address.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/Address/AddressFactory.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/Address/iAddress.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/Address/iAddressFactory.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/iProvider.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/iProvidersFactory.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/iProvidersSettings.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/iRequestDataFactory.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/iRequestSender.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Provider.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/ProvidersFactory.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/ProvidersSettings.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestDataFactory.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestSender.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Enums/DeliveryTypes.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Enums/OrderStatuses.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Enums/PickupTypes.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Enums/PointOperations.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Enums/PointTypes.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Enums/ProvidersKeys.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Exceptions/UnsupportedProviderKeyException.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/ModuleApi/Admin.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/ModuleApi/Common.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Orders/Collection.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Orders/ConstantMap.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Orders/Entity.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Orders/iCollection.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Orders/iEntity.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/providers/A1.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/providers/B2cpl.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/providers/Boxberry.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/providers/Cdek.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/providers/Dpd.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/providers/Hermes.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/providers/Iml.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/providers/Maxi.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/providers/Pickpoint.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/providers/Pony.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/providers/Spsr.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestData/CalculateDeliveryCost.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestData/ConnectProvider.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestData/iCalculateDeliveryCost.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestData/iConnectProvider.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestData/iSendOrder.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestData/SendOrder.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestDataParts/City.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestDataParts/DeliveryAgent.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestDataParts/iCity.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestDataParts/iDeliveryAgent.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestDataParts/iOrder.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestDataParts/iOrderCost.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestDataParts/iOrderItem.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestDataParts/Order.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/RequestDataParts/OrderItem.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Utils/ArgumentsValidator.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Utils/iArgumentsValidator.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Utils/iOrderStatusConverter.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/api/ApiShip/Utils/OrderStatusConverter.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/delivery.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/systems/courier.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/systems/russianpost.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/systems/self.php";
	$COMPONENTS[] = "./classes/components/emarket/classes/delivery/systems/ApiShip.php";
?>
