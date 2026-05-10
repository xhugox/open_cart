$(document).ready(function () {

    const pickupToggleButtonElem = $('#unisend_shipping_pickup_enabled');
    togglePickupAddress(pickupToggleButtonElem, false);
    $(pickupToggleButtonElem).on('change', function () {
        togglePickupAddress(this, true);
    })
    $('#unisend_shipping_settings_tabs li').on('click', function () {
        const tab = $(this).attr('data-value');
        if (tab) {
            $('#unisend_shipping_settings_active_tab').val(tab);
        }
    })
});

function togglePickupAddress(buttonElement, animation) {
    const selectedOption = $(buttonElement).find("option:selected");
    const enabled = selectedOption.attr('value');
    const pickupAddressContainerElem = $('#unisend_shipping_pickup_address_container');
    if (enabled == 1) {
        if (animation) {
            $(pickupAddressContainerElem).fadeIn();
        } else {
            $(pickupAddressContainerElem).show();
        }
    } else {
        if (animation) {
            $(pickupAddressContainerElem).fadeOut();
        } else {
            $(pickupAddressContainerElem).hide();
        }
    }
}