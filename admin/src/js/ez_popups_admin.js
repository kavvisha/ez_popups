jQuery(document).ready(function ($) {
    console.log('ez_popups_admin');

    //  ez_popups add selected pages on popup edit page
    $(document).on('click','.ez_popup_select_pages .ez_add_remove_pgs',function(){
        var parent_li = $(this).closest('li');
        var page_id = parent_li.attr('data_page');
        var selected_pages_list = $('#ez_popup_pages_selector').val();

        selected_pages_list += page_id+'_';
        $('#ez_popup_pages_selector').val(selected_pages_list);

        parent_li.hide();
        
        var inject__li = '<li data_page="'+page_id+'">'+parent_li.html()+'</li>';
        $('.ez_popup_selected_pages').find('ul.listpages').append(inject__li);     
    });

    $(document).on('click','.ez_popup_selected_pages .ez_add_remove_pgs',function(){
        var parent_li = $(this).closest('li');
        var page_id = parent_li.attr('data_page');
        var selected_pages_list = $('#ez_popup_pages_selector').val();

        selected_pages_list = selected_pages_list.replace(page_id,'');
        $('#ez_popup_pages_selector').val(selected_pages_list);

        parent_li.remove();
        
        // var inject__li = '<li data_page="'+page_id+'">'+parent_li.html()+'</li>';
        $('.ez_popup_select_pages').find('ul.listpages li[data_page="'+page_id+'"]').show();     
    });

    $(document).on('change','#ez_popup_pages_include_exclude', function(){
        var include_exclude = $(this).val();
        (include_exclude == 'all_pages') ? $('.section_ez_popup_select_pages').hide() : $('.section_ez_popup_select_pages').show(); 
    });


    $(window).on('load',function(){
        $('#ez_popups_options').removeClass('closed');
        $('#ez_popups_options .handle-actions').hide();
    });
});