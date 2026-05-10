$(document).ready(function(){
  window.unisendShipping = {};

  const terminalMatcher = (params, data) => {
    const originalMatcher = $.fn.select2.defaults.defaults.matcher;
    const result = originalMatcher(params, data);
    if (result && data.children && result.children && data.children.length) {
      if (data.children.length !== result.children.length && data.text.toLowerCase().includes(params.term.toLowerCase())) {
        result.children = data.children;
      }
      return result;
    }
    return null;
  }

  changeVisibilityOfCod();
  onParcelTypeChangedListener().then();

  async function handleTerminalsVisibility(animated = true) {
    const parcelPlan = $('#unisendshipping-plan-code-select').val();
    if (parcelPlan === "TERMINAL") {
      if (!window.unisendShipping.terminals) {
        await initTerminalsOptions();
      }
      if (animated) {
        $(".unisendshipping-terminals-select-container").slideDown();
      } else {
        $(".unisendshipping-terminals-select-container").show();
      }
    } else {
      if (animated) {
        $(".unisendshipping-terminals-select-container").slideUp();
      } else {
        $(".unisendshipping-terminals-select-container").hide();
      }
    }
  }

  async function onParcelTypeChangedListener() {
    var parcelPlanSelect = $('#unisendshipping-plan-code-select');
    var parcelTypeSelect = $('#unisendshipping-parcel-type-select');
    var parcelSizeField = $('#unisendshipping-size-select').parent().closest('div');
    var parcelWeightField = $('#unisendshipping-weight').parent().closest('div');
    var parcelPartCountField = $('#unisendshipping-part-count').parent().closest('div');
    parcelPlanSelect.on('change', async function () {
      await handleTerminalsVisibility();
    });
    await handleTerminalsVisibility(false);

    parcelTypeSelect.on('change', function () {
      const parcelType = $(this).val();
      if (parcelType === 'H2T' || parcelType === 'T2T' || parcelType === 'T2H' || parcelType === 'T2S') {
        $(parcelWeightField).slideUp();
        $(parcelPartCountField).slideDown();
      } else {
        $(parcelWeightField).slideDown();
        $(parcelPartCountField).slideUp();
      }
      if (parcelType === 'H2H') {
        $(parcelSizeField).slideUp();
        $(parcelPartCountField).slideDown();
      } else {
        $(parcelSizeField).slideDown();
      }
      if (parcelType === 'H2T' || parcelType === 'T2T' || parcelType === 'T2S') {
        parcelPlanSelect.val("TERMINAL");
      } else if (parcelType === 'H2H' || parcelType === 'T2H') {
        parcelPlanSelect.val("HANDS");
      }
    });
  }

  function registerListeners() {

    $(document).on('change', '#unisendshipping-cod-select', function() {
      changeVisibilityOfCod();
    });

    $('#unisendshipping-cod-select').on('popstate', function() {
      changeVisibilityOfCod();
    });
  }

  function changeVisibilityOfCod() {
    let codSelect = $('#unisendshipping-cod-select');
    let codAmountBox = ('#unisendshipping-cod-amount-box');
    let codValue = parseInt($(codSelect).val());

    if (codValue == 1) {
      $(codAmountBox).slideDown();
    } else {
      $(codAmountBox).slideUp();
    }
  }

  function loadTerminals() {
    return new Promise((resolve, reject) => {
      $.get('index.php?route=extension/shipping/unisend_shipping/terminals&' + userTokenParam + '&countryCode=' + countryCode, function (data) {
        window.unisendShipping.translations = data['translations'];
        window.unisendShipping.terminals = data['terminals'];
        return resolve(window.unisendShipping.terminals);
      }, 'json');
    });
  }

  async function initTerminalsOptions() {
    if (!window.unisendShipping.terminals) {
      await loadTerminals();
    }
    if (window.unisendShipping.terminals) {
      createTerminalsOptions(window.unisendShipping.terminals);
    }
  }

  function createTerminalsOptions(cityTerminals) {
    const data = cityTerminals.map(value => ({
      text: `${value.name}`, children: value.terminals.map(terminal => ({
        id: `${terminal.id}`,
        text: `${terminal.name}, ${terminal.address}`,
        selected: window.unisendShipping.selectedTerminalId && terminal.id === window.unisendShipping.selectedTerminalId
      })),
    }));
    $('.unisendshipping-terminals-select').select2({
      placeholder: window.unisendShipping.translations['text_shipping_unisend_shipping_checkout_select_parcel_locker_placeholder'],
      width: 'resolve',
      matcher(params, data) {
        return terminalMatcher(params, data);
      },
      data: data,
    });
    $(".unisendshipping-terminals-select").on("change", function () {
      const selectedOption = $(this).find("option:selected");
      const terminalId = selectedOption.attr('value');
      const terminalName = selectedOption.text();
      if (!isNaN(terminalId)) {
        $('#unisend-terminal').val(terminalName);
      }
    });
  }

  registerListeners();
});