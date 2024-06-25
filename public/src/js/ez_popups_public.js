jQuery(document).ready(function ($) {
    console.log('ez_popups_public');

    $(window).on('load', function () {
        if ($('div').hasClass('ez_popup_wrapper')) {
            var ez_popup_settings = (localStorage.getItem('ez_popup_settings')) ? JSON.parse(localStorage.getItem('ez_popup_settings')) : {};
            $('.ez_popup_wrapper').each(function (index) {
                console.log('ez_popup :', index);
                var this_popup = $(this);
                var popup_id = this_popup.attr('data_popup_id');
                var popup_delay = this_popup.attr('data_popup_delay');
                var popup_frequencey = this_popup.attr('data_popup_frequencey');
                var popup_start_date = new Date(this_popup.attr('data_popup_start_date'));
                var popup_end_date = new Date(this_popup.attr('data_popup_end_date'));
                var current_date = new Date();
                // every_page_load
                // once_a_day
                // once_a_week
                // once_a_month


                // if current date > popup_start_date	&&  current date < popup_end_date
                    // if every_page_load
                    //     show it anyways
                    // else
                    //     check the next_instance
                    //         if next_instance not there
                    //             show the popup
                    //         next_instance is there
                    //             check if current_time is greater than next_instance
                    //             show the popup
                    //             update next_instance

                var flag_open_popup = false;
                if ((current_date >= popup_start_date) && (current_date <= popup_end_date)) {
                    if(popup_frequencey == "every_page_load"){
                        flag_open_popup = true;
                    }
                    else{
                        var set_next_instance = new Date();
                        if(localStorage.getItem('ez_popup_next_instance_'+popup_id)){
                            var get_next_instance = new Date(localStorage.getItem('ez_popup_next_instance_'+popup_id));
                            if(current_date >= get_next_instance){
                                flag_open_popup = true;
                                if(popup_frequencey == "once_a_day"){
                                    set_next_instance = new Date(current_date.setDate(current_date.getDate() + 1));
                                }
                                else if(popup_frequencey == "once_a_week"){
                                    set_next_instance = new Date(current_date.setDate(current_date.getDate() + 7));
                                }
                                else if(popup_frequencey == "once_a_month"){
                                    set_next_instance = new Date(current_date.setMonth(current_date.getMonth() + 1));
                                }
                                localStorage.setItem('ez_popup_next_instance_'+popup_id,set_next_instance);
                            }
                            else{
                                flag_open_popup = false;
                            }
                        }
                        else{
                            flag_open_popup = true;
                            if(popup_frequencey == "once_a_day"){
                                set_next_instance = new Date(current_date.setDate(current_date.getDate() + 1));
                            }
                            else if(popup_frequencey == "once_a_week"){
                                set_next_instance = new Date(current_date.setDate(current_date.getDate() + 7));
                            }
                            else if(popup_frequencey == "once_a_month"){
                                set_next_instance = new Date(current_date.setMonth(current_date.getMonth() + 1));
                            }
                            localStorage.setItem('ez_popup_next_instance_'+popup_id,set_next_instance);
                        }
                        console.log('set_next_instance : ', set_next_instance);
                    }                  
                }
                else {
                    flag_open_popup = false;
                }

                console.log('flag_open_popup', flag_open_popup);
                if (flag_open_popup) {
                    ez_popup_settings[popup_id] = Date();
                    localStorage.setItem('ez_popup_settings', JSON.stringify(ez_popup_settings));

                    setTimeout(function () {
                        $('.ez_popup_wrapper').fadeIn();
                        $('body').css('overflow', 'hidden');
                    }, popup_delay);
                }

                // console.log('current_date '+popup_id+' : ', current_date);
                // console.log('popup_start_date '+popup_id+' : ', popup_start_date);
                // console.log('popup_end_date '+popup_id+' : ', popup_end_date);
                // console.log('popup_frequencey '+popup_id+' : ', popup_frequencey);
                // console.log('set_next_instance '+popup_id+' : ', set_next_instance);
            });
        }
    });

    $(document).on('click', '.close_ez_popup , #ez_popup_background', function () {
        $('.ez_popup_wrapper').fadeOut();
        $('body').css('overflow', 'auto');
    })
});