var $collectionHolder;
var $collectionHolderVid;
var $addNewItem = $("<a href='#' class='btn btn-info mb-3 add-item-button add-button-img'>Ajouter une image</a>");
var $addNewItemVideo = $("<a href='#' class='btn btn-info mb-3 add-item-button add-button-video'>Ajouter une vidéo</a>");

function addRemoveButton($card){
    var $removeButton = $("<a href='#' class='btn btn-danger'>Supprimer</a>");
    var $cardFooter = $("<div class='card-footer'></div>").append($removeButton);
    $card.append($cardFooter);

    $removeButton.click(function(e){
        e.preventDefault();
        $(e.target).parents('.card').remove();
    })
};

function addNewForm(target){
    var prototype = target.data('prototype');
    var index = target.data('index');
    var newForm = prototype.replace(/__name__/g, index);
    target.data('index', index + 1);

    var $card = $("<div class='card mb-3'></div>")
    var $cardBody = $("<div class='card-body'></div>").append(newForm);

    $card.append($cardBody);

    addRemoveButton($card);

    $card.insertBefore(target.find('.add-item-button')[0]);
    // target.find('.add-item-button')[0].insertBefore($card);
    // $addNewItem.
};

$(document).ready(function(){
    $collectionHolder = $('#img_list');
    $collectionHolderVid = $('#vid_list');

    $collectionHolder.append($addNewItem);
    $collectionHolderVid.append($addNewItemVideo);

    $collectionHolder.data('index', $collectionHolder.find('.card').length);
    $collectionHolderVid.data('index', $collectionHolderVid.find('.card').length);

    $addNewItem.click(function(e){
        e.preventDefault();
        addNewForm($collectionHolder);
    })

    $addNewItemVideo.click(function(e){
        e.preventDefault();
        addNewForm($collectionHolderVid);
    })

    $collectionHolder.find('.card').each(function(){
        addRemoveButton($(this));
    })
    $collectionHolderVid.find('.card').each(function(){
        addRemoveButton($(this));
    })
});
