$(document).ready(function () {
    $("input#search").keyup(function () {
        var productListID = $('input[name="productListID"]').val();
        var ajaxURL = $('input#ajaxURL').val();
        var searchString = $('input#search').val();
            $.ajax({
                type: 'POST',
                data: {
                    productListID: productListID,
                    searchString: searchString
                },
                url: ajaxURL,
                async: true,
                success: function (data) {
                    if (data.succes === true) {
                        var tableInnerHTML = '';
                        $.each(data.products, function (key, value) {
                            tableInnerHTML += '<tr>';
                            tableInnerHTML += '<td width="75">' + value.image + '</td>';
                            tableInnerHTML += '<td>' + value.naam + '</td>';
                            tableInnerHTML += '<td class="text-center"><span data-ajaxurl="' + data.ajaxURL + '" data-productlistid="' + productListID + '" data-productid="' + value.id + '"  class="glyphicon glyphicon-plus addProductToList" aria-hidden="true"></span></td>';
                            tableInnerHTML += '</tr>';
                        })
                        $('#productlistBody').html(tableInnerHTML);
                        $('table#productList').show();
                        $('div#productsTableSearchResult').show();
                        $('div#productsTable').hide();
                    } else {
                        $('#productlistBody').html('');
                        $('table#productList').hide();
                        $('div#productsTableSearchResult').hide();
                        $('div#productsTable').show();
                    }
                }
            });
    });

    $("#productlistBody, #productsBody").on("click", "span.addProductToList", function () {
        var productID = $(this).data('productid');
        var productListID = $(this).data('productlistid');

        var ajaxURL = $(this).data('ajaxurl');
        if (productID) {
            $.ajax({
                type: 'POST',
                data: {
                    productListID: productListID,
                    productID: productID,
                },
                url: ajaxURL,
                async: true,
                success: function (data) {
                    if (data.succes === true) {
                        var tableInnerHTML = '<tr id="productListDetail' + data.returnArray.productListDetailID + '" data-productlistdetailid="' + data.returnArray.productListDetailID + '">';
                        tableInnerHTML += '<td width="75">' + data.returnArray.image + '</td>';
                        tableInnerHTML += '<td>' + data.returnArray.naam + '</td>';
                        tableInnerHTML += '<td class="text-center" id="productQuantity' + productID + '">' + data.returnArray.quantity + '</td>';
                        tableInnerHTML += '<td class="text-center">';
                        tableInnerHTML += '<span class="glyphicon glyphicon-circle-arrow-up addProduct" data-modus="add" data-productlistdetailid="' + data.returnArray.productListDetailID + '" data-link="/productListAjax/addRemoveProduct" aria-hidden="true"></span>';
                        tableInnerHTML += '<span class="glyphicon glyphicon-circle-arrow-down removeProduct" data-modus="remove" data-productlistdetailid="' + data.returnArray.productListDetailID + '" data-link="/productListAjax/addRemoveProduct" aria-hidden="true"></span>';
                        tableInnerHTML += '</td>';
                        tableInnerHTML += '<td class="text-center">';
                        tableInnerHTML += '<a href="/product/detail/' + data.returnArray.id + '" class="btn btn-default" title="product bekijken"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a> ';
                        tableInnerHTML += '<a href="" data-link="/productListAjax/removeProduct" class="btn btn-default deleteProduct" title="product verwijderen van product lijst"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>';
                        tableInnerHTML += '</td>';
                        tableInnerHTML += '</tr>';
                        $('#bodyProductList').append(tableInnerHTML);
                        $('#noProductsOnList').hide();
                        $('#productTable').show();
                    } else {
                        if (data.succes = 'addQuantity') {
                            $('td#productQuantity' + data.returnArray.id).html(data.returnArray.quantity);
                        }
                    }

                }
            });
        }
    });


    $("#productTable").on("click", ".addProduct, .removeProduct", function () {
        var modus = $(this).data('modus');
        var ajaxURL = $(this).data('link');
        var productListDetailId = $(this).data('productlistdetailid');
        $.ajax({
            type: 'POST',
            data: {
                modus: modus,
                productListDetailId: productListDetailId
            },
            url: ajaxURL,
            async: true,
            success: function (data) {
                if (data.succes === true) {
                    $('td#productQuantity' + data.productQuantityId).html(data.quantity);
                } else {

                }

            }
        });
    });

    $("#productTable").on("click", ".deleteProduct", function (e) {
        e.preventDefault();
        var productListDetailId = $(this).parent('td').parent('tr').data('productlistdetailid');
        var ajaxURL = $(this).data('link');
        $.ajax({
            type: 'POST',
            data: {
                productListDetailId: productListDetailId
            },
            url: ajaxURL,
            async: true,
            success: function (data) {
                if (data.succes === true) {
                    $("tr#productListDetail" + productListDetailId).remove();
                    if ($('tbody#bodyProductList tr').size() == 0) {
                        $('p#noProductsOnList').show();
                    }
                } else {

                }

            }
        });
    });

});