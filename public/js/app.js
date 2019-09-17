var $collectionHolder;
var $addNewItem = $("<a href='#' class='btn btn-info mb-3'>Add new item</a>");

$(document).ready(function(){
    $collectionHolder = $('#img_list');
    $collectionHolder.append($addNewItem);
    $collectionHolder.data('index', $collectionHolder.find('.card').length);

    $addNewItem.click(function(e){
        e.preventDefault();
        addNewForm();
    })

    $collectionHolder.find('.card').each(function(){
        addRemoveButton($(this));
    })
});

function addNewForm(){
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var newForm = prototype.replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);
    var $card = $("<div class='card mb-3'></div>")
    var $cardBody = $("<div class='card-body'></div>").append(newForm);
    $card.append($cardBody);
    addRemoveButton($card);
    $addNewItem.before($card);

}

function addRemoveButton($card){
    var $removeButton = $("<a href='#' class='btn btn-danger'>Remove item</a>");
    var $cardFooter = $("<div class='card-footer'></div>").append($removeButton);
    $card.append($cardFooter);

    $removeButton.click(function(e){
        e.preventDefault();
        $(e.target).parents('.card').remove();
    })
};
