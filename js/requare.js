//---------------------------------------//// свг спрайт//---------------------------------------//;( function( window, document ){	'use strict';	var file     = '/img/svg-childrendetail.html',		revision = 1;	if( !document.createElementNS || !document.createElementNS( 'http://www.w3.org/2000/svg', 'svg' ).createSVGRect )		return true;	var isLocalStorage = 'localStorage' in window && window[ 'localStorage' ] !== null,		request,		data,		insertIT = function()		{			document.getElementById('svg-sprite-container').insertAdjacentHTML( 'afterbegin', data );		},		insert = function()		{			if( document.body ) insertIT();			else document.addEventListener( 'DOMContentLoaded', insertIT );		};//	if( isLocalStorage && localStorage.getItem( 'inlineSVGrev' ) == revision )//	{//		data = localStorage.getItem( 'inlineSVGdata' );//		if( data )//		{//			insert();//			return true;//		}//	}	try	{		request = new XMLHttpRequest();		request.open( 'GET', file, true );		request.onload = function()		{			if( request.status >= 200 && request.status < 400 )			{				data = request.responseText;				insert();				if( isLocalStorage )				{					localStorage.setItem( 'inlineSVGdata',  data );					localStorage.setItem( 'inlineSVGrev',   revision );				}			}		}		request.send();	}	catch( e ){}}( window, document ) );function autoFooter() {	if($('.js-footer').length) {		var wrapper = $('.wrapper'),			  footer = $('.js-footer'),		 		footerHeight = footer.outerHeight()		footer.css('margin-top',-footerHeight);		wrapper.css('padding-bottom',footerHeight);	}}function Map() {    if($('#map').length){        var uluru = {lat: 55.753292, lng: 37.575542};        var mapOption = new google.maps.Map(document.getElementById('map'),{            zoom: 16,            zoomControl: false,            streetViewControl: false,            mapTypeControl: false,            scrollwheel: false,            center: uluru,        });        var contentString = '<div id="content">'+            '<div id="siteNotice">'+            '</div>'+            '<strong id="firstHeading" class="firstHeading">Children details</strong>'+            '<div id="bodyContent">'+            '<div><b>Телефон</b></div>'+            '<div><a href="tel:+79062146984">7 906 214 69 84</a></div>'+            '<div><b>Почта</b></div>'+            '<a href="mailto:info@childrendetails.ru">info@childrendetails.ru</a>'+            '</div>'+            '</div>';        var infowindow = new google.maps.InfoWindow({          content: contentString,          maxWidth: 200        });        var marker = new google.maps.Marker({          position: uluru,          map: mapOption,          title: 'Uluru (Ayers Rock)'        });        marker.addListener('click', function() {          infowindow.open(mapOption, marker);        });    }}function getAjaxMacrosResult(macros, renderBlock){    $.ajax({        url:'/data/getAjaxMacrosResult/',        method:'POST',        data:{            'macros':macros        },        dataType: 'html',        success: function(answer){            $(renderBlock).html(answer);        }    });   }//---------------------------------------//// Подключаем набор написаных скриптов (requare/developRequare.js)//---------------------------------------//// выполняем их после того, как DOM построится $(document).ready(function() {        // Отоправляем при загрузке страницы, на случай если будет включено кеширование. Это позволит видеть актуальную корзину        getAjaxMacrosResult('%emarket cart("basket")%','.header__cart');	//---------------------------------------//	// Подключаем скрипты компонентов страниц ( modules )	//---------------------------------------//	$(function() {	    var $grid = $('.js-grid').masonry({	      itemSelector: '.js-grid-item',	      percentPosition: true,	      columnWidth: '.js-grid-sizer'	    });	});        /* Задание и сохранение в сессию направление сортировки каталога */	$(function() {            if($('#cat_order').length) {	        $(document).on('change','#cat_order',function(){                    var params = {};                    params.orderValue = $(this).val();                    $.ajax({                        'url':'/catalog/setOrderDirection/',                        'data':params,                        'method':'POST',                        success:function(answer){                            document.location.reload();                        }                    });                });	    }	});                	//---------------------------------------//	// Подключаем основные скрипты ( develop )	//---------------------------------------//	;(function() { 		initTextarea();	    if ($('.js-full-slider').length){	        $('.js-full-slider').slick({	            autoplay: true,	            prevArrow: '<span class="full-slider__arrow full-slider__arrow--prev"><svg class="full-slider__arrow-icon"><use xlink:href="#arrow-thin-left"></use></svg></span>',	            nextArrow: '<span class="full-slider__arrow full-slider__arrow--next"><svg class="full-slider__arrow-icon"><use xlink:href="#arrow-thin-right"></use></svg></span>'	        })	    };	    if ($('.js-slider').length){	        $('.js-slider').slick({	            infinite: false,	            slidesToShow: 4,	            slidesToScroll: 4,	            speed: 1000,	            prevArrow: '<span class="slider__arrow slider__arrow--prev"><svg class="slider__arrow-icon"><use xlink:href="#arrow-thin-left"></use></svg></span>',	            nextArrow: '<span class="slider__arrow slider__arrow--next"><svg class="slider__arrow-icon"><use xlink:href="#arrow-thin-right"></use></svg></span>'	        });	    }	    if ($('.js-product-slider-container').length){	        $('.js-product-slider').slick({	          slidesToShow: 1,	          slidesToScroll: 1,	          arrows: false,	          fade: true,	          asNavFor: '.js-product-slider-nav'	        });	        $('.js-product-slider-nav').slick({	          slidesToShow: 3,	          slidesToScroll: 1,	          asNavFor: '.js-product-slider',	          dots: false,	            centerMode: true,	          focusOnSelect: true,	          prevArrow: '<span class="slider__arrow slider__arrow--prev"><svg class="slider__arrow-icon"><use xlink:href="#arrow_left"></use></svg></span>',	          nextArrow: '<span class="slider__arrow slider__arrow--next"><svg class="slider__arrow-icon"><use xlink:href="#arrow_right"></use></svg></span>'	        });	    }		//dropdown	    if ($('.js-dropdown').length){	       $('.js-dropdown').hover(	            function() {	                $(this).find('.js-subnav').css({'visibility':'visible', 'opacity':'1'});	            },	            function() {	                $(this).find('.js-subnav').css({'visibility':'hidden', 'opacity':'0'});	            }	        );	    }	    //dropdown - end	    // select	    if($('select').length){	          $('select').select2({	              minimumResultsForSearch: Infinity	          });	          $('.js-select-type').select2({	              minimumResultsForSearch: Infinity,	              placeholder: "Цена (от высокой к низкой)"	          })	        $('.js-select-size').select2({	              minimumResultsForSearch: Infinity,	              placeholder: "Выберите размер"	          })	        $('.js-select-age').select2({	              minimumResultsForSearch: Infinity,	              placeholder: "Выберите Возраст"	          })	    };	//select - end	//back-to-top	if ($('.back-to-top').length){	     var limit     = $(window).height()/1,	       backToTop = $('.back-to-top');	     $(window).scroll(function () {	        if ($(this).scrollTop() > limit) {	            $('.back-to-top').fadeIn();	        } else {	            $('.back-to-top').fadeOut();	        } 	    });	    $('.back-to-top').click(function () {	        $('body,html').animate({	            scrollTop: 0	        }, 800);	        return false;	    });	}	//back-to-top - end	// modal	if ($(".js-popup").length){            var open = false;            $(".js-popup").fancybox({                hideOnClose: true,                toolbar  : false,                autoFocus: false            });        }                if($(".js-popups-close").length){            $(document).on('click','.js-popups-close',function (){                $.fancybox.close();            });        }                /* Добавлние товаров в корзину без перезагрузки */        if($('.btn__product-cart').length){            $('.btn__product-cart').on('click',function(e){                e.preventDefault();                var itemId = $(this).attr('rel');                var put_link = "/emarket/basket/put/element/"+itemId+"/";                var amount = $("#amount_"+itemId).val();                var params = {};    // Параметры для передачи на сервер в момент добавления товара в корзину                                var message = '';                var stopAdd = false;                $('.option--field').each(function(index, element){                    if($(element).val() == 0){                        message = 'Пожалуйста, выберите "'+$(element).attr('label')+'"';                        stopAdd = true;                    }else{                        params[$(element).attr('name')] = $(element).val();     // Добавляем опции к товару                    }                });                                if(true === stopAdd){                    alert(message)                    return false;                }                                params.amount = amount; // Добавляемое кол-во                                $.ajax({                    url: '/udata'+put_link+'.json',                    method: 'POST',                    dataType: 'json',                    data:params,                    success: function(data) {                      getAjaxMacrosResult('%emarket cart("basket")%','.header__cart');                        $.fancybox.close();                        $.fancybox.open({                            src  : '#carts',                            type : 'inline',                            toolbar  : false,                            beforeShow: function(){                                // Обновление информации во всплывающем блоке                                getAjaxMacrosResult('%emarket cart("addPopup")%','.popup__wrapper__content');                            }                        });                    },                    error: function(data) {                      //console.log(data)                    },                    complete: function(data) {                      //console.log(data)                    }                });                            });        }            	    $("a[href$='.png'], a[href$='.jpg'], [data-fancybox]").fancybox({	        infobar : false,	        slideShow  : false,	        titleShow : true,	        'titlePosition'     : 'inside'	    });                                    $('.js-popups-oneclick').on('click', function(e){                e.preventDefault();                                var params = {};                params.id = $(this).attr('rel');                params.name = $("#oneclick__name").val();                params.phone = $("#oneclick__phone").val();                                $.ajax({                    url:'/emarket/oneClickOrder/',                    'data':params,                    'method':'POST',                    dataType:'json',                    success:function(answer){                        if(answer.status == 'error'){                            $("#"+answer.fldID).trigger('focus');                        }                                                if(answer.status == 'ok'){                            e.preventDefault();                            $.fancybox.close();                            $.fancybox.open({                                src  : '#thank',                                type : 'inline',                                toolbar  : false                            });                        }                    }                });            });            	    $('.js-popups-true').on('click', function(e){	        e.preventDefault();	        $.fancybox.close();	        $.fancybox.open({	            src  : '#thank',	            type : 'inline',	            toolbar  : false            });	});	//modal - end	    if ($(".js-spinner").length){                	        $(".js-spinner").spinner();	    }	// autoHeight	if ($('.js-height').length){	    $('.js-height').matchHeight({	        byRow: true,	        property: 'height',	        target: null,	        remove: false 	    });	}                if($('.delivery-id')){            $(document).on('change','.delivery-id',function (){                var rel = $(this).attr('rel');                if(rel == 'free'){                    $('.ordering__form-address').html('');                }else{                    getAjaxMacrosResult('%emarket customerDeliveryList("onestep")%','.ordering__form-address');                }            });        }                $(document).on('change','input[rel=new_adress]',function () {            $("#delivery-address-new").attr('checked',true);            $("#delivery-address-new").trigger('change');        });                $(document).on('change','.profile__page_adress',function () {            var value = $(this).val();            params = {};            params.id = value;            $.ajax({                url:'/users/getAdressData/',                'data':params,                'method':'POST',                dataType:'json',                success:function(answer){                    var id = answer['id'];                    $("#city").attr('name','data[new][city]').val(answer['city']);                    $("#index").attr('name','data[new][index]').val(answer['index']);                    $("#street").attr('name','data[new][street]').val(answer['street']);                    $("#house").attr('name','data[new][house]').val(answer['house']);                }            });        });                	//    jcf.replaceAll();	// autoHeight - end	}());	function initTextarea(){	    $(document).on('input', '.js-search', function () {	        var $search = $('.js-search');	        if ($('.js-search').val()) {	            $('.js-placeholderDiv').hide();	        } else {	            $('.js-placeholderDiv').show();	        }	    });	    $('.js-placeholderDiv').on('click', function() {	        $(this).closest('.js-placeholder-container').find('.js-search').trigger('focus');	    });	}    Map();});$(window).load( function() {	autoFooter();    Map();     $(".js-sticky-header").sticky({ topSpacing: 0 });});$(window).on('resize',function() {	autoFooter();//    $grid.imagesLoaded().progress( function() {//    $grid.masonry()});});