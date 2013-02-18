if (typeof(Tags) === 'undefined') {
    var Tags = {};
}

Tags.handleKeyPress = function(e,form)
{
    var key=e.keyCode || e.which;
    if (key==13) {
        document.getElementById('add_tags_button').click();
    }
}