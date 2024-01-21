$("#modeSwitcher").on("click", function (e) {
    e.preventDefault(), modeSwitch(), location.reload()
}), $(".collapseSidebar").on("click", function (e) {
    $(".vertical").hasClass("narrow") ? $(".vertical").toggleClass("open") : ($(".vertical").toggleClass("collapsed"), $(".vertical").hasClass("hover") && $(".vertical").removeClass("hover")), e.preventDefault()
}), $(".sidebar-left").hover(function () {
    $(".vertical").hasClass("collapsed") && $(".vertical").addClass("hover"), $(".narrow").hasClass("open") || $(".vertical").addClass("hover")
}, function () {
    $(".vertical").hasClass("collapsed") && $(".vertical").removeClass("hover"), $(".narrow").hasClass("open") || $(".vertical").removeClass("hover")
}), $(".toggle-sidebar").on("click", function () {
    $(".navbar-slide").toggleClass("show")
}),
    function (a) {
        a(".dropdown-menu a.dropdown-toggle").on("click", function (e) {
            return a(this).next().hasClass("show") || a(this).parents(".dropdown-menu").first().find(".show").removeClass("show"), a(this).next(".dropdown-menu").toggleClass("show"), a(this).parents("li.nav-item.dropdown.show").on("hidden.bs.dropdown", function (e) {
                a(".dropdown-submenu .show").removeClass("show")
            }), !1
        })
    }(jQuery), $(".navbar .dropdown").on("hidden.bs.dropdown", function () {
        $(this).find("li.dropdown").removeClass("show open"), $(this).find("ul.dropdown-menu").removeClass("show open")
    }), $(".file-panel .card").on("click", function () {
        $(this).hasClass("selected") ? ($(this).removeClass("selected"), $(this).find("bg-light").removeClass("shadow-lg"), $(".file-container").removeClass("collapsed")) : ($(this).addClass("selected"), $(this).addClass("shadow-lg"), $(".file-panel .card").not(this).removeClass("selected"), $(".file-container").addClass("collapsed"))
    }), $(".close-info").on("click", function () {
        $(".file-container").hasClass("collapsed") && ($(".file-container").removeClass("collapsed"), $(".file-panel").find(".selected").removeClass("selected"))
    }), $(function () {
        $(".info-content").stickOnScroll({
            topOffset: 0,
            setWidthOnStick: !0
        })
    });
/**
 * Generate random number
 */
function uuid() {
    return Math.floor(Math.random() * 99999) + '-' + Date.now() + '-' + Math.floor(Math.random() * 99999);
}


var refInterval = window.setInterval('getSyncStatus()', 1000); // 30 seconds

var getSyncStatus = function () {
    if (typeof syncStatus !== 'undefined') {
        if (parseInt(syncStatus) < 100) {
            $.ajax(getPatientSyncStatus, {
                type: 'GET', // http method
                data: {}, // data to submit
                dataType: 'json',
                success: function (response, status, xhr) {
                    var per = parseInt(response.progress) + "%";
                    $(".progress .progress-bar").css('width', per);
                    if (response.status == 1) {
                        if (parseInt(response.progress) == 100) {
                            $(".sync-progress").remove();
                        }
                    }
                },
                error: function (jqXhr, textStatus, errorMessage) {
                    $('p').append('Error' + errorMessage);
                }
            });
        }
    }
};
getSyncStatus();

/*
    Get Departments
*/
function getDepartments(hospital) {
    $.blockUI({ title: 'Please wait while fetching Departments' });
    $.ajax(getDepartmentUrl, {
        type: 'POST', // http method
        data: { hospital: hospital }, // data to submit
        dataType: 'json',
        success: function (response, status, xhr) {
            var departmentHtml = "<option value=''>-- Select --</option>";
            if (response.status == 1) {
                $.each(response.data, function (i, j) {
                    departmentHtml += '<option value="' + j + '">' + i + '</option>';
                });
            }
            $('.department').html(departmentHtml);
            $.unblockUI();
        },
        error: function (jqXhr, textStatus, errorMessage) {
            $('p').append('Error' + errorMessage);
        }
    });
}

function getDoctors(department, hospital) {
    $.blockUI();
    $.ajax(getDoctorUrl, {
        type: 'POST', // http method
        data: { hospital: hospital, department: department }, // data to submit
        dataType: 'json',
        success: function (response, status, xhr) {
            // console.log(response);

            var doctorHtml = "<option value=''>-- Select --</option>";
            // name;
            if (response.status == 1) {
                // 
                // console.log(response.data);
                if (jQuery.isEmptyObject(response.data)) {
                    toastr.error('No doctor is available for this selection', 'Oops!');
                } else {
                    $.each(response.data, function (i, j) {
                        doctorHtml += '<option value="' + i + '">' + j + '</option>';
                    });
                }
            }
            $('.doctors').html(doctorHtml);
            $.unblockUI();
        },
        error: function (jqXhr, textStatus, errorMessage) {
            $('p').append('Error' + errorMessage);
        }
    });
}

/**
 * Get Slot intervals of a doctor
 * @param {integer} doctor_id 
 * @param {date} date 
 */
function getSlots(hospital_id, department_id, doctor_id, date, shift_name) {
    $.blockUI();
    var slotHtml = "";
    $.ajax(getSlotUrl, {
        type: 'POST', // http method
        data: { hospital: hospital_id, department: department_id, doctor: doctor_id, date: date, shift: shift_name }, // data to submit
        dataType: 'json',
        success: function (response, status, xhr) {
            // console.log(response);
            if (response.status == 1) {
                $.each(response.data, function (i, j) {
                    // console.log(i);
                    console.log(j.time);
                    var status = "";
                    if (j.status == false) {
                        status = " diabled-slot";
                    }
                    if (j.is_disabled == true) {
                        status = " diabled-slot invalid";
                    }
                    slotHtml += '<div class="timeslot-box' + status + ' ' + j.meridiem + '"><input class="form-check-input" type="radio" name="time" id="time' + i + '" value="' + j.time + '"><label class="form-check-label" for="time' + i + '">' +
                        j.time + '</label></div>';
                });
            } else {
                slotHtml = '<div class="row"><div class="alert alert-danger center">Sorry! No slot is available</div></div>';
            }
            $(".timeslot-filler").html(slotHtml);
            $.unblockUI();
        },
        error: function (jqXhr, textStatus, errorMessage) {
            $('p').append('Error' + errorMessage);
        }
    });
}

/**
 * Get Slot intervals of a doctor
 * @param {integer} doctor_id 
 * @param {date} date 
 */
function getShifts(doctor_id, date) {
    $.blockUI();
    var shiftHtml = '<option value="">--Select--</option>';
    $.ajax(getShiftUrl, {
        type: 'POST', // http method
        data: { doctor: doctor_id, date: date }, // data to submit
        dataType: 'json',
        success: function (response, status, xhr) {
            // console.log(response);
            if (response.status == 1) {
                $.each(response.data, function (i, j) {
                    console.log(i);
                    console.log(j);
                    // var status = "";
                    // if (j.status == false) {
                    //     status = " diabled-slot";
                    // }
                    shiftHtml += '<option value="' + i + '">' + j + '</option>';
                });
            }
            $(".shifts").html(shiftHtml);
            $.unblockUI();
        },
        error: function (jqXhr, textStatus, errorMessage) {
            $('p').append('Error' + errorMessage);
        }
    });
}

function getAvailabilityDates(doctor_id) {
    $.blockUI();
    var shiftHtml = '<option value="">--Select--</option>';
    $.ajax(getAvailabilityDatesUrl, {
        type: 'POST', // http method
        data: { doctor: doctor_id }, // data to submit
        dataType: 'json',
        success: function (response, status, xhr) {
            if (response.status == 1) {
                // $(".appointment_date").prop( "disabled", false );
                console.log(response.start);
                console.log(response.end);
                $(".appointment_date").datepicker('option', 'minDate', new Date(response.start));
                $(".appointment_date").datepicker('option', 'maxDate', new Date(response.end));
                // $(".appointment_date").datepicker({
                //     format: 'YYYY-MM-DD',
                //     useCurrent: false,
                //     showClose: true,
                //     minDate: '2021-05-01',
                //     maxDate: '2021-07-15',
                // })
            }
            $.unblockUI();
        },
        error: function (jqXhr, textStatus, errorMessage) {
            $('p').append('Error' + errorMessage);
        }
    });
}

/**
 * Delete a fetaure associated to a department
 * @param {*} id Department's Fetaure ID
 */
function deleteFeature(id) {
    if (!confirm("Are you sure to delete this item?")) {
        return false;
    } else {
        $.ajax(deleteFeatureUrl, {
            type: 'POST', // http method
            data: { feature: id }, // data to submit
            dataType: 'json',
            success: function (response, status, xhr) {
                if (response.status == 1) {
                    $(".row" + id).remove();
                }
            },
            error: function (jqXhr, textStatus, errorMessage) {
                $('p').append('Error' + errorMessage);
            }
        });
    }
}