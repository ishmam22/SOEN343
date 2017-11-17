$(document).ready(() => {
    const formUnits = {
        unitForm: $('#serial-next'),
        itemNextBtn: [
            'monitor-serial-next-btn',
            'desktop-serial-next-btn',
            'laptop-serial-next-btn',
            'tablet-serial-next-btn'
        ],
        hideNext: function() {
            formUnits.unitForm.removeClass('hidden');
        },
        displayNext: function () {
            formUnits.unitForm.addClass('hidden');
        },
        monitor: {
            form: $('form#monitor-form-units'),
            unitInput: $('#monitor-units'),
            modal: '.addUnitsMonitorLink',
            nextBtn: 'monitor-serial-next-btn',
            emptyVal: function () {
                formUnits.monitor.form.find('#units-inputs-container').empty();
                formUnits.monitor.unitInput.val(0);
                formUnits.hideNext();
            }
        },
        desktop: {
            form: $('form#desktop-form-units'),
            unitInput: $('#desktop-units'),
            modal: '.addUnitsDesktopLink',
            nextBtn: 'monitor-serial-next-btn',
            emptyVal: function () {
                formUnits.desktop.form.find('#units-inputs-container').empty();
                formUnits.desktop.unitInput.val(0);
                formUnits.hideNext();
            }
        },
        laptop: {
            form: $('form#laptop-form-units'),
            unitInput: $('#laptop-units'),
            modal: '.addUnitsLaptopLink',
            nextBtn: 'monitor-serial-next-btn',
            emptyVal: function () {
                formUnits.laptop.form.find('#units-inputs-container').empty();
                formUnits.laptop.unitInput.val(0);
                formUnits.hideNext();
            }
        },
        tablet: {
            form: $('form#tablet-form-units'),
            unitInput: $('#tablet-units'),
            modal: '.addUnitsTabletLink',
            nextBtn: 'monitor-serial-next-btn',
            emptyVal: function () {
                formUnits.tablet.form.find('#units-inputs-container').empty();
                formUnits.tablet.unitInput.val(0);
                formUnits.hideNext();
            }
        }
    };
    $(".modal"+formUnits.monitor.modal+"").on("hidden.bs.modal", function() {
        formUnits.monitor.emptyVal();
    });
    $(".modal"+formUnits.desktop.modal+"").on("hidden.bs.modal", function() {
        formUnits.desktop.emptyVal();
    });
    $(".modal"+formUnits.laptop.modal+"").on("hidden.bs.modal", function() {
        formUnits.laptop.emptyVal();
    });
    $(".modal"+formUnits.tablet.modal+"").on("hidden.bs.modal", function() {
        formUnits.tablet.emptyVal();
    });
    $('input[name=monitor-serial]').on('click', function () {
        let fields = formUnits.monitor.unitInput.val();
        serialInputs(fields, formUnits.monitor.form);
        formUnits.displayNext();
    });
    $('input[name=laptop-serial]').on('click', function () {
        let fields = formUnits.laptop.unitInput.val();
        serialInputs(fields, formUnits.laptop.form);
        formUnits.displayNext();
    });
    $('input[name=desktop-serial]').on('click', function () {
        let fields = formUnits.desktop.unitInput.val();
        serialInputs(fields, formUnits.desktop.form);
        formUnits.displayNext();
    });
    $('input[name=tablet-serial]').on('click', function () {
        let fields = formUnits.tablet.unitInput.val();
        serialInputs(fields, formUnits.tablet.form);
        formUnits.displayNext();
    });
});

function serialInputs(fields, form) {
    let h = null;
    for(let i = 0; i < fields; i++){
        form.find('#units-inputs-container').append(`<div class="form-group serial-number">
        <label>Serial #</label>
        <input type="text" id="serial-number" value="${randomString()}" class="form-control">
        </div>`);
    }
    return h;
}
function randomString() {
    let chars ='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    let len = 5;
    let result = '';
    for (let i = len; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
    return result;
}