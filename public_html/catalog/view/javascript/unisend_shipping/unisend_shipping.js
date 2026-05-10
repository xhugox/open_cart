const terminalMatcher = (params, data) => {
  const originalMatcher = $.fn.select2.defaults.defaults.matcher;
  const result = originalMatcher(params, data);
  if (
      result &&
      data.children &&
      result.children &&
      data.children.length
  ) {
    if (
        data.children.length !== result.children.length &&
        data.text.toLowerCase().includes(params.term.toLowerCase())
    ) {
      result.children = data.children;
    }
    return result;
  }
  return null;
}

$.fn.bindFirst = function (name, fn) {
  this.on(name, fn);
  this.each(function () {
    var handler, handlers;
    handlers = $._data(this, 'events')[name.split('.')[0]];
    handler = handlers.pop();
    handlers.splice(0, 0, handler);
  });
};

(function( $ ) {
	'use strict';

  window.unisendShipping = {};
  window.unisendShipping.countryTerminals = [];
  $(document).delegate('#button-loggedorder, #qc_confirm_order, #quick-checkout-button-confirm, #button-checkout, #button-shipping-method, #button-guest', 'click', function (e) {
    return processValidation(this, e);
  });
  $(document).on('change', '.address-country select[name=country_id], select[name=personal_details\\[country_id\\]], select#input-signup-country, select#payment_address_country_id', function (e) {
    onCountryChange(this);
  });
  $(document).on('change', '#input-payment-address, select[name=payment_details\\[address_id\\]], select[name=shipping_address\\[address_id\\]], input[name=shipping_address\\[address_id\\]]', function (e) {
    handleAddressChange(this);
  });

  $(document).delegate('#button-guest-shipping, #button-shipping-address', 'click', function (e) {
    delete window.unisendShipping.terminals;
    setTimeout(() => {
      processSelectedShippingMethod();
    }, 2000);
  });


  $(document).ready(function () {

    const elementsToListen = '#checkout-checkout, .content-delivery-method, .quick-checkout-wrapper, #d_quickcheckout, #collapse-shipping-method, #content .panel-group, .opc_shipping_method, .shipping-method-panel, .mp-checkout, #content';
    const elementMatcher = {nodeName: 'INPUT', checked: true, name: 'shipping_method'};
    startListeningForMutations(elementsToListen, elementMatcher, function (element) {
      processSelectedShippingMethod();
      applyShippingMethodImg();
    });

    $(elementsToListen).on('change', function () {
      processSelectedShippingMethod();
    });

    setTimeout(() => {
      bindFirstElements();
    }, 1000);

  });

  function onCountryChange(element) {
    const selectedOption = $(element).find('option:selected');
    if (selectedOption.length > 0) {
      window.unisendShipping.selectedCountryId = selectedOption.val();
      handleAddressChange(false);
    }
  }

  function handleAddressChange(element = false) {
    if (element) {
      const selectedOption = $(element).find('option:selected');
      if (selectedOption.length > 0) {
        window.unisendShipping.selectedAddressId = selectedOption.val();
      }
    }
    if (isUnisendTerminalMethodSelected()) {
      delete window.unisendShipping.terminals;
      delete window.unisendShipping.shippingMethod;
      getTerminalsOptionsElement().remove();
      processSelectedShippingMethod();
    }
  }

  function stopEvent(event) {
    event.stopImmediatePropagation();
    event.preventDefault();
    journalPreloaderStop();
    if (typeof preloaderStop === "function") {
      setTimeout(() => {
        preloaderStop();
      }, 1000)
    }
  }

  function journalPreloaderStop() {
    if ($(".journal-loading-overlay").length) {
      $(".journal-loading-overlay").hide();
      $("#quick-checkout-button-confirm").button("reset");
      $([document.documentElement, document.body]).animate({
        scrollTop: $("input[type=radio][name=shipping_method]:checked").offset().top - 200
      }, 1000);
    }
  }

  function processSelectedShippingMethod() {
    const shippingMethodValue = getShippingMethodValue();
    if (shippingMethodValue) {
      const terminalOptionsElm = getTerminalsOptionsElement();
      if (window.unisendShipping.shippingMethod === shippingMethodValue && terminalOptionsElm.length > 0) {
        return;
      }
      window.unisendShipping.shippingMethod = shippingMethodValue;
      terminalOptionsElm.remove();
      createUnisendTerminalsOptions();
    }
  }

  function getShippingMethodValue() {
    const shippingMethodElement = getShippingMethodElement();
    if (shippingMethodElement.length > 0) {
      return shippingMethodElement.val();
    }
    return '';
  }

  function getShippingMethodElement() {
    return $('input[type=radio][name=shipping_method]:checked');
  }

  function getTerminalsOptionsElement() {
    return $('#unisend_shipping_terminals_options');
  }

  function bindFirstElements() {
    bindFirstButtonConfirm();
    bindFirstOrderConfirm();
  }

  function bindFirstButtonConfirm() {
    $('#button-confirm').bindFirst('click', function (e) {
      return processValidation(this, e);
    });
  }

  function bindFirstOrderConfirm() {
    $('#confirm_order').bindFirst('click', function (e) {
      return processValidation(this, e);
    });
  }

  function displayApiError() {
    getShippingMethodElement().parent().append("<div class='alert alert-danger alert-dismissible'>" + window.unisendShipping.error + "<button type='button' class='close' style='padding-right: 0.4em;' data-dismiss='alert'>&times;</button></div>");
  }

  function resetApiError() {
    delete window.unisendShipping.error;
  }

  function apiErrorExists() {
    return window.unisendShipping.error != null;
  }

  function isJournalCheckout() {
    return $('#quick-checkout-button-confirm').length > 0;
  }

  function isJournalElement(element) {
    return $(element).attr('id') === 'quick-checkout-button-confirm';
  }

  function doProcessApiValidation(element) {
    if (apiErrorExists()) {
      return false;
    }
    if (isJournalCheckout() && !isJournalElement(element)) {
      return false;
    }
    return true;
  }

  function processValidation(element, event) {
    if (!isUnisendMethodSelected()) {
      return;
    }
    if (doProcessApiValidation(element)) {
      apiValidate();
    }
    if (apiErrorExists()) {
      if (!isJournalElement(element)) {
        displayApiError();
        resetApiError();
      } else {
        bindFirstElements();
      }
      stopEvent(event);
      return false;
    }
    return true;
  }

  function isUnisendMethodSelected() {
    const targetValue = getShippingMethodValue().split(':')[0];
    return targetValue === 'unisend_shipping.unisend_shipping_shipping' || targetValue === 'unisend_shipping.unisend_shipping_terminal';
  }

  function isUnisendTerminalMethodSelected() {
    const targetValue = getShippingMethodValue().split(':')[0];
    return targetValue === 'unisend_shipping.unisend_shipping_terminal';
  }

  function applyShippingMethodImg() {
    $("input[name='shipping_method'][value^=unisend_shipping]").each((index, input) => {
      const parentLabel = $(input).closest('label');
      if (parentLabel.length !== 0) {
        if (parentLabel.find('img').length === 0) {
          $("<img src='/image/catalog/unisend_shipping/unisend_shipping_lpexpress_logo_45x25.png'>").insertAfter(input);
        }
      } else {
        const nextLabel = $(input).next().filter((index, element) => $(element).prop('nodeName') === 'LABEL');
        if (nextLabel.length !== 0 && nextLabel.find('img').length === 0) {
          $(nextLabel).append("<img src='/image/catalog/unisend_shipping/unisend_shipping_lpexpress_logo_45x25.png'>");
        }
      }
    });
  }

  function createUnisendTerminalsOptions() {
    const target = getShippingMethodElement();
    const targetValue = target.val().split(':')[0];
    if (targetValue === 'unisend_shipping.unisend_shipping_terminal') {
      if ($('#unisend_shipping_terminals_options optgroup').length === 0) {
        const targetLabel = $("label[for='" + target.val() + "']");
        let targetElement;
        if (targetLabel.length > 0) {
          targetElement = targetLabel;
        } else {
          targetElement = target.parent();
        }
        $(targetElement).append("<div id='unisend_shipping_terminals_options' style='display: none;'><select class='unisend_shipping_terminals_options' name='unisend_shipping_terminals_options'><option></option></select><div style='clear: both; padding-top: 15px; text-align: right;'></div></div><div style='margin: 15px 0; display: none; height: 300px;'></div>");
        showOptions();
      }
      $('#unisend_shipping_terminals_options').show();
    }
  }

  async function showOptions() {
    if (!window.unisendShipping.terminals) {
      await loadTerminals();
    }
    if (window.unisendShipping.terminals) {
      createTerminalsOptions(window.unisendShipping.terminals);
    }
  }

  function createTerminalsOptions(cityTerminals) {
    const data = cityTerminals.map(value => ({
      text: `${value.name}`,
      children: value.terminals.map(terminal => ({
        id: `${terminal.id}`,
        text: `${terminal.name}, ${terminal.address}`,
        selected: window.unisendShipping.selectedTerminalId && terminal.id === window.unisendShipping.selectedTerminalId
      })),
    }));
    $('.unisend_shipping_terminals_options').select2({
      placeholder: window.unisendShipping.translations['text_shipping_unisend_shipping_checkout_select_parcel_locker_placeholder'],
      width: 'resolve',
      matcher(params, data) {
        return terminalMatcher(params, data);
      },
      data: data,
      });
    $(".unisend_shipping_terminals_options").on("change", function () {
      const selectedOption = $(this).find("option:selected");
      const terminalId = selectedOption.attr('value');
      const terminalName = selectedOption.text();
      if (!isNaN(terminalId)) {
        saveSelectedTerminal(terminalId, terminalName);
      }
    });
    }

  function loadTerminals() {
    const selectedCountryId = getSelectedCountry();
    const selectedAddressId = getSelectedAddressId();
    const terminalCountryId = selectedCountryId != false ? selectedCountryId : selectedAddressId;
    if (terminalCountryId != false && window.unisendShipping.countryTerminals[terminalCountryId]) {
      return new Promise((resolve, reject) => {
        window.unisendShipping.terminals = window.unisendShipping.countryTerminals[terminalCountryId];
        return resolve(window.unisendShipping.terminals);
      });
    }
    return new Promise((resolve, reject) => {
      $.ajax({
        type: 'post',
        url: 'index.php?route=extension/shipping/unisend_shipping/terminals',
        dataType: 'json',
        data: {
          selectedCountryId: selectedCountryId,
          selectedAddressId: selectedAddressId
        },
        success: function (data) {
          if (data) {
            window.unisendShipping.translations = data['translations'];
            window.unisendShipping.countryTerminals[terminalCountryId] = data['terminals'];
            window.unisendShipping.terminals = data['terminals'];
          }
        return resolve(window.unisendShipping.terminals);
        }
      });
    });
  }

  function getSelectedCountry() {
    return window.unisendShipping.selectedCountryId ?? 0;
  }

  function getSelectedAddressId() {
    return window.unisendShipping.selectedAddressId ?? 0;
  }

  function appendInputElementData(formData, elementName, customName = elementName, parent = null) {
    const nameSelector = elementName ? "[name^='" + elementName + "']" : "";
    const parentSelector = (parent ? parent + " " : "" + "");
    const selector = parentSelector + "input" + nameSelector;
    $(selector).each(function (i, element) {
      const value = $(element).val();
      if (value && typeof value === 'string' && value.trim()) {
        const name = $(element).attr('name').replace(elementName + '.', '').replace(elementName + '[', '').replace(']', '');
        formData.append(customName + '[' + name + ']', $(element).val());
      }
    });
  }

  function appendSelectOptionElementData(formData, elementName, customName = elementName, parent = null) {
    const nameSelector = elementName ? "[name^='" + elementName + "']" : "";
    const parentSelector = (parent ? parent + " " : "" + "");
    const selector = parentSelector + "select" + nameSelector;
    $(selector).each(function (i, element) {
      const optionElement = $(this).children('option:selected');
      const value = $(optionElement).val();
      if (value && typeof value === 'string' && value.trim()) {
        const name = $(element).attr('name').replace(elementName + '.', '');
        formData.append(customName + '[' + name + ']', $(element).val());
      }
    });
  }

  function apiValidate() {
    var formData = new FormData;
    appendInputElementData(formData, "payment_address");
    appendInputElementData(formData, "shipping_address");
    appendInputElementData(formData, "signup", "shipping_address");
    appendInputElementData(formData, "personal_details", "shipping_address");
    appendInputElementData(formData, "", "shipping_address", "#payment_address_table");
    appendInputElementData(formData, "", "shipping_address", "#shipping_address_table");
    appendSelectOptionElementData(formData, "payment_address");
    appendSelectOptionElementData(formData, "shipping_address");
    appendSelectOptionElementData(formData, "signup", "shipping_address");
    appendSelectOptionElementData(formData, "personal_details", "shipping_address");
    appendSelectOptionElementData(formData, "", "shipping_address", "#payment_address_table");
    appendSelectOptionElementData(formData, "", "shipping_address", "#shipping_address_table");
    formData.append('shipping_method', window.unisendShipping.shippingMethod);
    if (window.unisendShipping.selectedTerminalId) {
      formData.append('terminalId', window.unisendShipping.selectedTerminalId);
      formData.append('terminalName', window.unisendShipping.selectedTerminalName);
    }

    $.ajax({
      url: 'index.php?route=extension/shipping/unisend_shipping/validate',
      type: "POST",
      async: false,
      processData: false,
      contentType: false,
      data: formData,
      success: function (data) {
        if (data['error']) {
          window.unisendShipping.error = data['error'];
        }
      }
    });
  }

  function saveSelectedTerminal(terminalId, terminalName) {
    window.unisendShipping.selectedTerminalId = terminalId;
    window.unisendShipping.selectedTerminalName = terminalName;
    $.post("index.php?route=extension/shipping/unisend_shipping/save_selected_terminal",
        {
          name: terminalName,
          id: terminalId
        }
    )
  }

  function startListeningForMutations(elements, matcher, callback) {

    const recurse = (parent) => {

      for (let match in matcher) {
        if (parent[match] !== matcher[match]) {
          if (parent.childNodes) {
            [...parent.childNodes].forEach(recurse);
          }
          return;
        }
      }
      callback(parent);
    };

    // select the target node
    var target = $(elements);

    if (target) {
      // create an observer instance
      var observer = new MutationObserver(function (mutations) {
        //loop through the detected mutations(added controls)
        mutations.forEach(function (mutation) {
          for (const node of mutation.addedNodes) {
            recurse(node);
          }
        });
      });
      let obsConfig = {
        childList: true,
        characterData: true,
        attributes: true,
        subtree: true
      };
      target.each(function () {
        observer.observe(this, obsConfig);
      });

      // later, you can stop observing
      //observer.disconnect();
    }
  }
})( jQuery );
