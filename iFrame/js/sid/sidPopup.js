/*
 * Copyright (c) 2020 PayGate (Pty) Ltd
 *
 * Author: App Inlet (Pty) Ltd
 * 
 * Released under the GNU General Public License
 */
function SidEft(sid_merchant, sid_currency, sid_country, sid_reference, sid_buyer_reference, sid_amount, sid_default_reference, sid_custom_01, sid_custom_02, sid_custom_03, sid_custom_04, sid_custom_05, sid_consistent) {
    var params = {
        SID_MERCHANT: sid_merchant,
        SID_CURRENCY: sid_currency,
        SID_COUNTRY: sid_country,
        SID_REFERENCE: sid_reference,
        SID_BUYER_REFERENCE: sid_buyer_reference,
        SID_DEFAULT_REFERENCE: sid_default_reference,
        SID_AMOUNT: sid_amount,
        SID_CUSTOM_01: sid_custom_01,
        SID_CUSTOM_02: sid_custom_02,
        SID_CUSTOM_03: sid_custom_03,
        SID_CUSTOM_04: sid_custom_04,
        SID_CUSTOM_05: sid_custom_05,
        SID_CONSISTENT: sid_consistent
    };
    SidEftJson(params);
}

function SidEftJson(jsonData) {
    try {
        var params = JSON.parse(jsonData);
    } catch (err) {
        var params = jsonData;
    }
    jQuery(".btn-checkout").after("<div id='sidPopup'></div>");
    jQuery("#sidPopup").append("<div id='sidPopupContent'></div>");
    var queryString = jQuery.param(params);
    jQuery("#sidPopupContent").append("<iframe id='sidPopupFrame' src='https://www.sidpayment.com/paysidv20/#/bank?" + queryString + "&POPUP=true&Continue=%20%20'></iframe>");
}

function createSidOrder(baseurl) {
    jQuery(".btn-checkout").prop('disabled', true);
    jQuery.ajax({
        url: baseurl + "/sid/redirect/createOrder",
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        }
    }).done(function(data) {
        var json = JSON.parse(data);
        SidEft(json['SID_MERCHANT'], json['SID_CURRENCY'], json['SID_COUNTRY'], json['SID_REFERENCE'], '', json['SID_AMOUNT'], '', json['SID_CUSTOM_01'], json['SID_CUSTOM_02'], '', '', '', json['SID_CONSISTENT']);
    });
}