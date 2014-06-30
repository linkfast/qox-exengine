/**
 * QOX ExEngine Message Agent
 * Created by Giancarlo Chiappe Aguilar on 25/04/14.
 */
/* startup */
setTimeout(function() {
    viewportHeight = parseInt($('#eema-sidebar').css('height').replace('px',''));
},10);
if (typeof module_init !== 'undefined') {
    console.log('module_init','defined, running.');
    setTimeout(module_init,20);
} else {
    console.log('module_init','not defined.');
}