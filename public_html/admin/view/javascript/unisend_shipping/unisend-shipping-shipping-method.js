const parcelTypes = $(
  "#input-shipping_unisend_shipping_method_parcel_type"
);
const planCodes = $(
  "#input-shipping_unisend_shipping_method_plan_code"
);

const rateType = $(
  "#input-shipping_unisend_shipping_method_rate_type"
);

let countries = [];

let size = 0;

function addSize(from, to, price) {
  let html = '<tr>';

  const fromEl = from ? '  <td class="text-left"><input type="text" name="unisend_shipping_weight_from_' + size + '" value="' + from + '" class="form-control"/></td>' : '  <td class="text-left"><input type="text" name="unisend_shipping_weight_from_' + size + '" class="form-control"/></td>';
  html += fromEl;

  const toEl = to ? '  <td class="text-left"><input type="text" name="unisend_shipping_weight_to_' + size + '" value="' + to + '" class="form-control"/></td>' : '  <td class="text-left"><input type="text" name="unisend_shipping_weight_to_' + size + '" class="form-control"/></td>';
  html += toEl;

  const priceEl = price ? '  <td class="text-left"><input type="text" name="unisend_shipping_weight_price_' + size + '" value="' + price + '" class="form-control"/></td>' : '  <td class="text-left"><input type="text" name="unisend_shipping_weight_price_' + size + '" class="form-control"/></td>';
  html += priceEl;

  html += '  <td class="text-right"><button type="button" onclick="remove(this);" data-toggle="tooltip" title="'+ textButtonRemove +'" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '</tr>';
  size++;

  $('.parcel-type-weights').append(html);
}

function remove(element) {
  $(element).parent().parent().remove();
  size--;
}

parcelTypes.on("change", function () {
  const shippingPlan = shippingPlans.find((e) => e.code == planCodes.val());
  if (!shippingPlan) {
    return;
  }
  const shipping = shippingPlan.shipping.find(
    (e) => e.parcelType == this.value
  );

  const parcelTypesSizes = $(".parcel-type-sizes");
  parcelTypesSizes.empty();

  rateType.children().each(function () {
    $(this).hide();
  });

  rateType.find("#carrier").show();

  if (shipping.requirements.size) {
    rateType.find("#size").show();

    let sizeSortOrder = [];
    sizeSortOrder['XS'] = 1;
    sizeSortOrder['S'] = 2;
    sizeSortOrder['M'] = 3;
    sizeSortOrder['L'] = 4;
    sizeSortOrder['XL'] = 5;
    shipping.options.sort(function(o1, o2) {
      var o1SortOrder = sizeSortOrder[o1.size.code];
      var o2SortOrder = sizeSortOrder[o2.size.code];
      if (!o1SortOrder || !o2SortOrder) {
        return 0;
      }
      return o1SortOrder - o2SortOrder;
    });

    $.each(shipping.options, function (index, option) {
      var row = $("<tr>").append(
        $("<td>").text(option.size.code),
        $("<td>").append(
          $("<input>")
            .attr("type", "text")
            .attr("name", "unisend_shipping_size_" + option.size.code)
            .attr("class", "form-control")
        )
      );
      parcelTypesSizes.append(row);
    });
  }
  if (shipping.requirements.weight) {
    rateType.find("#weight").show();
  }
  rateType.trigger("change");

  countries = [];
});

planCodes.on("change", function () {
  const shippingPlan = shippingPlans.find((e) => e.code == this.value);
  if (!shippingPlan) {
    return;
  }

  parcelTypes.empty();

  $.each(shippingPlan.shipping, function (index, shippingPlan) {
    var option = $("<option></option>")
      .attr("value", shippingPlan.parcelType)
      .text(shippingPlan.name);
    parcelTypes.append(option);
  });
  parcelTypes.trigger("change");
});

rateType.on("change", function (v) {
  const sizeEl = $(".rate-type-size");
  const wiegthEl = $(".rate-type-weight");

  if (rateType.val() == "weight") {
    sizeEl.hide();
    wiegthEl.show();
  }
  if (rateType.val() == "size") {
    wiegthEl.hide();
    sizeEl.show();
  }
  if (rateType.val() == "carrier") {
    wiegthEl.hide();
    sizeEl.hide();
  }
});

$("#input-shipping_unisend_shipping_method_country").autocomplete({
  source: function (request, response) {
    if (countries.length) {
      response(
        countries.filter((x) =>
          x.label.toUpperCase().startsWith(request.toUpperCase())
        )
      );
    } else {
      $.ajax({
        url:
          "index.php?route=extension/shipping/unisend_shipping/planCountries&" +
          userTokenParam +
          "&planCodes=" +
          planCodes.val() +
          "&parcelTypes=" +
          parcelTypes.val(),
        dataType: "json",
        success: function (json) {
          countries = $.map(json, function (item) {
            return {
              label: item["name"],
              value: item["code"],
            };
          });
          response(countries);
        },
      });
    }
  },
  select: function (item) {
    $("#input-shipping_unisend_shipping_method_country").val("");
    addCountry(item);
  },
});

function addCountry(item){
  $("#method_country").append(
    '<div id="method_country' +
      item["value"] +
      '"><i class="fa fa-minus-circle" onclick="$(\'#method_country' +
      item["value"] +
      "').remove();\"></i> " +
      item["label"] +
      '<input type="hidden" name="method_country[]" value="' +
      item["value"] +
      '" /></div>'
  );
}

if (editedShippingMethod) {
  planCodes.val(editedShippingMethod.planCode);
}

planCodes.trigger("change");

if (editedShippingMethod) {
  parcelTypes.val(editedShippingMethod.parcelType);
  parcelTypes.trigger("change");
  rateType.val(editedShippingMethod.rateType);
  rateType.trigger("change");
  for (const size of editedShippingMethodSizes) {
    $('input[name="unisend_shipping_size_' + size.size + '"]').val(
      size.price
    );
  }
  
  for (const weight of editedShippingMethodWeights) {
    addSize(weight.weight_from, weight.weight_to, weight.price);
  }

  for (const country in editedShippingMethodCountries) {    
    addCountry({
      label:editedShippingMethodCountries[country].name,
      value:editedShippingMethodCountries[country].code
    });
  }

  $('#input-shipping_unisend_shipping_method_free_shipping_from').val(editedShippingMethod.freeShippingFrom);

}

function deleteShippingMethod(action)
{
  const editUrl = new URLSearchParams(window.location.href);
  const id = editUrl.get('id');

  const deleteUrl = new URLSearchParams(action);
  deleteUrl.set('id',id);

  $('#form-shipping').attr("action", decodeURIComponent(deleteUrl.toString()));
  $('#form-shipping').submit();
}