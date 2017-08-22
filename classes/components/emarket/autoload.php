<?php
	$moduleDirName = dirname(__FILE__);
	$classes = [
		'discount'	=> [
			$moduleDirName . '/classes/discounts/discount.php'
		],

		'discountModificator'	=> [
			$moduleDirName . '/classes/discounts/discountModificator.php'
		],

		'itemDiscountRule'	=> [
			$moduleDirName . '/classes/discounts/iItemDiscountRule.php'
		],

		'orderDiscountRule'	=> [
			$moduleDirName . '/classes/discounts/iOrderDiscountRule.php'
		],

		'discountRule'	=> [
			$moduleDirName . '/classes/discounts/discountRule.php'
		],

		'order'	=> [
			$moduleDirName . '/classes/orders/order.php'
		],

		'orderItem'	=> [
			$moduleDirName . '/classes/orders/orderItem.php'
		],

		'iOrderNumber'	=> [
			$moduleDirName . '/classes/orders/number/iOrderNumber.php'
		],

		'delivery'	=> [
			$moduleDirName . '/classes/delivery/delivery.php'
		],

		'payment'	=> [
			$moduleDirName . '/classes/payment/payment.php'
		],

		'customer'	=> [
			$moduleDirName . '/classes/customer/customer.php'
		],

		'emarketTop'	=> [
			$moduleDirName . '/classes/stat/emarketTop.php'
		],

		'UmiCms\Classes\Components\Emarket\Orders\Calculator'	=> [
			$moduleDirName . '/classes/orders/Calculator.class.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestSender'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestSender.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\iRequestSender'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/iRequestSender.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\ProvidersFactory'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/ProvidersFactory.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\ProvidersSettings'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/ProvidersSettings.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Provider'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/Provider.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Orders\Collection'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/Orders/Collection.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Orders\iCollection'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/Orders/iCollection.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Orders\ConstantMap'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/Orders/ConstantMap.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestData\CalculateDeliveryCost'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestData/CalculateDeliveryCost.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestData\SendOrder'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestData/SendOrder.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestData\ConnectProvider'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestData/ConnectProvider.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestData\iCalculateDeliveryCost'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestData/iCalculateDeliveryCost.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestData\iSendOrder'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestData/iSendOrder.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestDataParts\iOrder'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestDataParts/iOrder.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestDataParts\iOrderCost'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestDataParts/iOrderCost.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestDataParts\iDeliveryAgent'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestDataParts/iDeliveryAgent.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestDataParts\iOrderItem'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestDataParts/iOrderItem.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestDataParts\iCity'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestDataParts/iCity.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestData\iConnectProvider'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestData/iConnectProvider.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\iProvidersFactory'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/iProvidersFactory.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\iProvider'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/iProvider.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestDataFactory'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestDataFactory.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\iRequestDataFactory'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/iRequestDataFactory.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Orders\iEntity'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/Orders/iEntity.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\iProvidersSettings'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/iProvidersSettings.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestDataParts\Order'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestDataParts/Order.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestDataParts\OrderCost'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestDataParts/OrderCost.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestDataParts\DeliveryAgent'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestDataParts/DeliveryAgent.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestDataParts\OrderItem'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestDataParts/OrderItem.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\RequestDataParts\City'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/RequestDataParts/City.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Orders\Entity'	=> [
			$moduleDirName . '/classes/delivery/api/ApiShip/Orders/Entity.php'
		],

		'ApiShipDelivery'	=> [
			$moduleDirName . '/classes/delivery/systems/ApiShip.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Providers\A1' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/providers/A1.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Providers\B2cpl' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/providers/B2cpl.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Providers\Boxberry' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/providers/Boxberry.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Providers\Cdek' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/providers/Cdek.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Providers\Dalli' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/providers/Dalli.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Providers\Dpd' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/providers/Dpd.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Providers\Hermes' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/providers/Hermes.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Providers\Iml' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/providers/Iml.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Providers\Maxi' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/providers/Maxi.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Providers\Pickpoint' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/providers/Pickpoint.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Providers\Pony' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/providers/Pony.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Providers\Spsr' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/providers/Spsr.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Enums\OrderStatuses' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/Enums/OrderStatuses.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Enums\DeliveryTypes' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/Enums/DeliveryTypes.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Enums\PickupTypes' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/Enums/PickupTypes.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Enums\PointOperations' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/Enums/PointOperations.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Enums\PointTypes' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/Enums/PointTypes.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Enums\ProvidersKeys' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/Enums/ProvidersKeys.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Utils\OrderStatusConverter' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/Utils/OrderStatusConverter.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Utils\iOrderStatusConverter' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/Utils/iOrderStatusConverter.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Utils\ArgumentsValidator' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/Utils/ArgumentsValidator.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Utils\iArgumentsValidator' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/Utils/iArgumentsValidator.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Exceptions\UnsupportedProviderKeyException' => [
			$moduleDirName . '/classes/delivery/api/ApiShip/Exceptions/UnsupportedProviderKeyException.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\Address\Address' => [
			$moduleDirName . '/classes/delivery/Address/Address.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\Address\iAddress' => [
			$moduleDirName . '/classes/delivery/Address/iAddress.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\Address\AddressFactory' => [
			$moduleDirName . '/classes/delivery/Address/AddressFactory.php'
		],

		'UmiCms\Classes\Components\Emarket\Delivery\Address\iAddressFactory' => [
			$moduleDirName . '/classes/delivery/Address/iAddressFactory.php'
		]
	];
