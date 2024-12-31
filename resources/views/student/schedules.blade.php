@extends('layouts.app')

@section('title', 'Thời khoá biểu')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/schedule.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .container-shadow {
        background-color: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .form-control {
        width: 230px !important;
    }
</style>
@endsection

@section('content')
<div class="container mt-3 container-shadow">
    <div class="m-3">
        <h3 class="text-danger text-center">THỜI KHÓA BIỂU</h3>
        <div class="d-flex mt-3 justify-content-center gap-2 align-items-center w-100">

            <span>Chọn niên học:</span>
            @php
                use Carbon\Carbon;
                $now = Carbon::now(); // Get the current date and time
            @endphp

            <select id="StudyYearDetailId" name="StudyYearDetailId" class="form-control">
                @foreach($studyYearDetails as $detail)
                    @php
                        // Check if the current date is within the StartYear and EndYear range
                        $startYear = Carbon::create($detail->StartYear, 1, 1);
                        $endYear = Carbon::create($detail->EndYear, 12, 31);
                        $isSelected = $now->between($startYear, $endYear) ? 'selected' : '';
                    @endphp
                    <option value="{{ $detail->Id }}" {{ $isSelected }}>
                        {{ $detail->StartYear }} - {{ $detail->EndYear }}
                    </option>
                @endforeach
            </select>


            <span>Học kỳ:</span>
            <select id="SemesterId" name="SemesterId" class="form-control" required>
                <option value="">Chọn học kỳ</option>
            </select>

            <span>Tuần:</span>
            <select id="WeekIndex" name="WeekIndex" class="form-control" required>
            </select>
        </div>
    </div>
    <div class="cd-schedule loading">
        <!-- Schedule content dynamically loaded -->
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        function formatDate(dateStr) {
            const dateParts = dateStr.split('/');
            return `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
        }

        $('#WeekIndex').on('change', function () {
            const selectedOption = $(this).find(':selected').text().split('|')[1]?.trim();
            if (selectedOption) {
                const [startDate, endDate] = selectedOption.split('-').map(d => d.trim());
                $.ajax({
                    url: '{{ route("student.getSchedules") }}',
                    type: 'GET',
                    data: {
                        startDate: formatDate(startDate),
                        endDate: formatDate(endDate)
                    },
                    success: function (data) {
                        $('.cd-schedule').html(data);
                        createSchedules(); // Initialize schedule UI
                    },
                    error: function () {
                        alert('Không thể lấy dữ liệu lịch trình.');
                    }
                });
            }
        });

        $('#SemesterId').on('change', function () {
            const semesterId = $(this).val();
            if (semesterId) {
                $.ajax({
                    url: '{{ route("lecturer.getWeekBySemester") }}',
                    type: 'GET',
                    data: { semesterId },
                    success: function (data) {
                        const weekSelect = $('#WeekIndex');
                        weekSelect.empty();
                        const now = new Date();

                        data.forEach(item => {
                            const startDate = new Date(item.ngayDauTuan);
                            const endDate = new Date(item.ngayCuoiTuan);
                            const formattedStart = startDate.toLocaleDateString('vi-VN');
                            const formattedEnd = endDate.toLocaleDateString('vi-VN');
                            const selected = (now >= startDate && now <= endDate) ? ' selected' : '';
                            weekSelect.append(`<option value="${item.thuTuTuan}"${selected}>Tuần ${item.thuTuTuan} | ${formattedStart} - ${formattedEnd}</option>`);
                        });

                        $('#WeekIndex').trigger('change');
                    },
                    error: function () {
                        alert('Không thể lấy dữ liệu tuần.');
                    }
                });
            }
        });

        $('#StudyYearDetailId').on('change', function () {
            const yearId = $(this).val();
            if (yearId) {
                $.ajax({
                    url: '{{ url("/admin/getsemesterbyyearid") }}/' + yearId,
                    type: 'GET',
                    data: { yearDetailId: yearId },
                    success: function (data) {
                        const semesterSelect = $('#SemesterId');
                        semesterSelect.empty();
                        const now = new Date();

                        data.forEach(item => {
                            const startDate = new Date(item.startDate);
                            const endDate = new Date(item.endDate);
                            const selected = (now >= startDate && now <= endDate) ? ' selected' : '';
                            semesterSelect.append(`<option value="${item.Id}"${selected}>${item.Name}</option>`);
                        });

                        $('#SemesterId').trigger('change');
                    },
                    error: function () {
                        alert('Không thể lấy dữ liệu học kỳ.');
                    }
                });
            }
        });

        $('#StudyYearDetailId').trigger('change');
    });
</script>
<script>

    var transitionEnd = 'webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend';
    var transitionsSupported = ($('.csstransitions').length > 0);
    //if browser does not support transitions - use a different event to trigger them
    if (!transitionsSupported) transitionEnd = 'noTransition';

    //should add a loding while the events are organized

    function SchedulePlan(element) {
        this.element = element;
        this.timeline = this.element.find('.timeline');
        this.timelineItems = this.timeline.find('li');
        this.timelineItemsNumber = this.timelineItems.length;
        this.timelineStart = getScheduleTimestamp(this.timelineItems.eq(0).text());
        //need to store delta (in our case half hour) timestamp
        this.timelineUnitDuration = getScheduleTimestamp(this.timelineItems.eq(1).text()) - getScheduleTimestamp(this.timelineItems.eq(0).text());

        this.eventsWrapper = this.element.find('.events');
        this.eventsGroup = this.eventsWrapper.find('.events-group');
        this.singleEvents = this.eventsGroup.find('.single-event');
        this.eventSlotHeight = this.eventsGroup.eq(0).children('.top-info').outerHeight();

        this.modal = this.element.find('.event-modal');
        this.modalHeader = this.modal.find('.header');
        this.modalHeaderBg = this.modal.find('.header-bg');
        this.modalBody = this.modal.find('.body');
        this.modalBodyBg = this.modal.find('.body-bg');
        this.modalMaxWidth = 800;
        this.modalMaxHeight = 480;

        this.animating = false;

        this.initSchedule();
    }

    SchedulePlan.prototype.initSchedule = function () {
        this.scheduleReset();
        this.initEvents();
    };

    SchedulePlan.prototype.scheduleReset = function () {
        var mq = this.mq();
        if (mq == 'desktop' && !this.element.hasClass('js-full')) {
            //in this case you are on a desktop version (first load or resize from mobile)
            this.eventSlotHeight = this.eventsGroup.eq(0).children('.top-info').outerHeight();
            this.element.addClass('js-full');
            this.placeEvents();
            this.element.hasClass('modal-is-open') && this.checkEventModal();
        } else if (mq == 'mobile' && this.element.hasClass('js-full')) {
            //in this case you are on a mobile version (first load or resize from desktop)
            this.element.removeClass('js-full loading');
            this.eventsGroup.children('ul').add(this.singleEvents).removeAttr('style');
            this.eventsWrapper.children('.grid-line').remove();
            this.element.hasClass('modal-is-open') && this.checkEventModal();
        } else if (mq == 'desktop' && this.element.hasClass('modal-is-open')) {
            //on a mobile version with modal open - need to resize/move modal window
            this.checkEventModal('desktop');
            this.element.removeClass('loading');
        } else {
            this.element.removeClass('loading');
            this.placeEvents();
        }
    };

    SchedulePlan.prototype.initEvents = function () {
        var self = this;

        // this.singleEvents.each(function() {
        // 	var durationLabel = '<span class="event-date">' + $(this).data('start') + ' - ' + $(this).data('end') + '</span>';
        // 	$(this).children('a').prepend($(durationLabel));
        // });

        this.singleEvents.each(function () {
            // create the .event-date element for each event
            var durationLabel = '<span class="event-date">' + $(this).data('start') + ' - ' + $(this).data('end') + '</span>';
            $(this).children('a').prepend($(durationLabel));

            // detect click on the event and open the modal
            $(this).on('click', 'a', function (event) {
                event.preventDefault();
                if (!self.animating) self.openModal($(this)); // Gọi hàm mở modal
            });
        });

        //close modal window
        this.modal.on('click', '.close', function (event) {
            event.preventDefault();
            if (!self.animating) self.closeModal(self.eventsGroup.find('.selected-event'));
        });
        this.element.on('click', '.cover-layer', function (event) {
            if (!self.animating && self.element.hasClass('modal-is-open')) self.closeModal(self.eventsGroup.find('.selected-event'));
        });
    };

    SchedulePlan.prototype.placeEvents = function () {
        var self = this;
        this.singleEvents.each(function () {
            //place each event in the grid -> need to set top position and height
            var start = getScheduleTimestamp($(this).attr('data-start')),
                duration = getScheduleTimestamp($(this).attr('data-end')) - start;

            var eventTop = self.eventSlotHeight * (start - self.timelineStart) / self.timelineUnitDuration,
                eventHeight = self.eventSlotHeight * duration / self.timelineUnitDuration;

            $(this).css({
                top: (eventTop - 1) + 'px',
                height: (eventHeight - 2) + 'px'
            });
        });

        this.element.removeClass('loading');
    };

    SchedulePlan.prototype.openModal = function (event) {
        var self = this;
        var mq = self.mq();
        this.animating = true;

        //update event name and time
        this.modalHeader.find('.event-name').text(event.find('.event-name').text());
        this.modalHeader.find('.event-date').text(event.find('.event-date').text());
        this.modal.attr('data-event', event.parent().attr('data-event'));

        //Thông tin học phần
        var Sclass = event.parent().data('class');
        var code = event.parent().data('code');
        var room = event.parent().data('room');
        var period = event.parent().data('period');
        var sTime = event.parent().data('start');
        var eTime = event.parent().data('end');

        var eventDetails = `
                                                <p><strong>Mã học phần:</strong> ${code}</p>
                                                <p><strong>Lớp:</strong> ${Sclass}</p>
                                                <p><strong>Phòng học:</strong> ${room}</p>
                                                <p><strong>Tiết:</strong> ${period}</p>
                                                <p><strong>Thời gian:</strong> ${sTime} - ${eTime}</p>
                                            `;

        this.modalBody.find('.event-info').html(eventDetails);


        //update event content
        self.element.addClass('content-loaded');


        this.element.addClass('modal-is-open');

        setTimeout(function () {
            //fixes a flash when an event is selected - desktop version only
            event.parent('li').addClass('selected-event');
        }, 10);

        if (mq == 'mobile') {
            self.modal.one(transitionEnd, function () {
                self.modal.off(transitionEnd);
                self.animating = false;
            });
        } else {
            var eventTop = event.offset().top - $(window).scrollTop(),
                eventLeft = event.offset().left,
                eventHeight = event.innerHeight(),
                eventWidth = event.innerWidth();

            var windowWidth = $(window).width(),
                windowHeight = $(window).height();

            var modalWidth = (windowWidth * .8 > self.modalMaxWidth) ? self.modalMaxWidth : windowWidth * .8,
                modalHeight = (windowHeight * .8 > self.modalMaxHeight) ? self.modalMaxHeight : windowHeight * .8;

            var modalTranslateX = parseInt((windowWidth - modalWidth) / 2 - eventLeft),
                modalTranslateY = parseInt((windowHeight - modalHeight) / 2 - eventTop);

            var HeaderBgScaleY = modalHeight / eventHeight,
                BodyBgScaleX = (modalWidth - eventWidth);

            //change modal height/width and translate it
            self.modal.css({
                top: eventTop + 'px',
                left: eventLeft + 'px',
                height: modalHeight + 'px',
                width: modalWidth + 'px',
            });
            transformElement(self.modal, 'translateY(' + modalTranslateY + 'px) translateX(' + modalTranslateX + 'px)');

            //set modalHeader width
            self.modalHeader.css({
                width: eventWidth + 'px',
            });
            //set modalBody left margin
            self.modalBody.css({
                marginLeft: eventWidth + 'px',
            });

            //change modalBodyBg height/width ans scale it
            self.modalBodyBg.css({
                height: eventHeight + 'px',
                width: '1px',
            });
            transformElement(self.modalBodyBg, 'scaleY(' + HeaderBgScaleY + ') scaleX(' + BodyBgScaleX + ')');

            //change modal modalHeaderBg height/width and scale it
            self.modalHeaderBg.css({
                height: eventHeight + 'px',
                width: eventWidth + 'px',
            });
            transformElement(self.modalHeaderBg, 'scaleY(' + HeaderBgScaleY + ')');

            self.modalHeaderBg.one(transitionEnd, function () {
                //wait for the  end of the modalHeaderBg transformation and show the modal content
                self.modalHeaderBg.off(transitionEnd);
                self.animating = false;
                self.element.addClass('animation-completed');
            });
        }

        //if browser do not support transitions -> no need to wait for the end of it
        if (!transitionsSupported) self.modal.add(self.modalHeaderBg).trigger(transitionEnd);
    };

    SchedulePlan.prototype.closeModal = function (event) {
        var self = this;
        var mq = self.mq();

        this.animating = true;

        if (mq == 'mobile') {
            this.element.removeClass('modal-is-open');
            this.modal.one(transitionEnd, function () {
                self.modal.off(transitionEnd);
                self.animating = false;
                self.element.removeClass('content-loaded');
                event.removeClass('selected-event');
            });
        } else {
            var eventTop = event.offset().top - $(window).scrollTop(),
                eventLeft = event.offset().left,
                eventHeight = event.innerHeight(),
                eventWidth = event.innerWidth();

            var modalTop = Number(self.modal.css('top').replace('px', '')),
                modalLeft = Number(self.modal.css('left').replace('px', ''));

            var modalTranslateX = eventLeft - modalLeft,
                modalTranslateY = eventTop - modalTop;

            self.element.removeClass('animation-completed modal-is-open');

            //change modal width/height and translate it
            this.modal.css({
                width: eventWidth + 'px',
                height: eventHeight + 'px'
            });
            transformElement(self.modal, 'translateX(' + modalTranslateX + 'px) translateY(' + modalTranslateY + 'px)');

            //scale down modalBodyBg element
            transformElement(self.modalBodyBg, 'scaleX(0) scaleY(1)');
            //scale down modalHeaderBg element
            transformElement(self.modalHeaderBg, 'scaleY(1)');

            this.modalHeaderBg.one(transitionEnd, function () {
                //wait for the  end of the modalHeaderBg transformation and reset modal style
                self.modalHeaderBg.off(transitionEnd);
                self.modal.addClass('no-transition');
                setTimeout(function () {
                    self.modal.add(self.modalHeader).add(self.modalBody).add(self.modalHeaderBg).add(self.modalBodyBg).attr('style', '');
                }, 10);
                setTimeout(function () {
                    self.modal.removeClass('no-transition');
                }, 20);

                self.animating = false;
                self.element.removeClass('content-loaded');
                event.removeClass('selected-event');
            });
        }

        //browser do not support transitions -> no need to wait for the end of it
        if (!transitionsSupported) self.modal.add(self.modalHeaderBg).trigger(transitionEnd);
    }

    SchedulePlan.prototype.mq = function () {
        //get MQ value ('desktop' or 'mobile')
        var self = this;
        return window.getComputedStyle(this.element.get(0), '::before').getPropertyValue('content').replace(/["']/g, '');
    };

    SchedulePlan.prototype.checkEventModal = function (device) {
        this.animating = true;
        var self = this;
        var mq = this.mq();

        if (mq == 'mobile') {
            //reset modal style on mobile
            self.modal.add(self.modalHeader).add(self.modalHeaderBg).add(self.modalBody).add(self.modalBodyBg).attr('style', '');
            self.modal.removeClass('no-transition');
            self.animating = false;
        } else if (mq == 'desktop' && self.element.hasClass('modal-is-open')) {
            self.modal.addClass('no-transition');
            self.element.addClass('animation-completed');
            var event = self.eventsGroup.find('.selected-event');

            var eventTop = event.offset().top - $(window).scrollTop(),
                eventLeft = event.offset().left,
                eventHeight = event.innerHeight(),
                eventWidth = event.innerWidth();

            var windowWidth = $(window).width(),
                windowHeight = $(window).height();

            var modalWidth = (windowWidth * .8 > self.modalMaxWidth) ? self.modalMaxWidth : windowWidth * .8,
                modalHeight = (windowHeight * .8 > self.modalMaxHeight) ? self.modalMaxHeight : windowHeight * .8;

            var HeaderBgScaleY = modalHeight / eventHeight,
                BodyBgScaleX = (modalWidth - eventWidth);

            setTimeout(function () {
                self.modal.css({
                    width: modalWidth + 'px',
                    height: modalHeight + 'px',
                    top: (windowHeight / 2 - modalHeight / 2) + 'px',
                    left: (windowWidth / 2 - modalWidth / 2) + 'px',
                });
                transformElement(self.modal, 'translateY(0) translateX(0)');
                //change modal modalBodyBg height/width
                self.modalBodyBg.css({
                    height: modalHeight + 'px',
                    width: '1px',
                });
                transformElement(self.modalBodyBg, 'scaleX(' + BodyBgScaleX + ')');
                //set modalHeader width
                self.modalHeader.css({
                    width: eventWidth + 'px',
                });
                //set modalBody left margin
                self.modalBody.css({
                    marginLeft: eventWidth + 'px',
                });
                //change modal modalHeaderBg height/width and scale it
                self.modalHeaderBg.css({
                    height: eventHeight + 'px',
                    width: eventWidth + 'px',
                });
                transformElement(self.modalHeaderBg, 'scaleY(' + HeaderBgScaleY + ')');
            }, 10);

            setTimeout(function () {
                self.modal.removeClass('no-transition');
                self.animating = false;
            }, 20);
        }
    };
    
    var objSchedulesPlan = [],
        windowResize = false;

    function checkResize() {
        objSchedulesPlan.forEach(function (element) {
            element.scheduleReset();
        });
        windowResize = false;
    }

    function createSchedules() {
        var schedules = $('.cd-schedule');

        objSchedulesPlan = [];
        if (schedules.length > 0) {
            schedules.each(function () {
                //create SchedulePlan objects
                objSchedulesPlan.push(new SchedulePlan($(this)));
            });
        }

        $(window).on('resize', function () {
            if (!windowResize) {
                windowResize = true;
                (!window.requestAnimationFrame) ? setTimeout(checkResize) : window.requestAnimationFrame(checkResize);
            }
        });

        $(window).keyup(function (event) {
            if (event.keyCode == 27) {
                objSchedulesPlan.forEach(function (element) {
                    element.closeModal(element.eventsGroup.find('.selected-event'));
                });
            }
        });

        
    }

    function getScheduleTimestamp(time) {
        //accepts hh:mm format - convert hh:mm to timestamp
        time = time.replace(/ /g, '');
        var timeArray = time.split(':');
        var timeStamp = parseInt(timeArray[0]) * 60 + parseInt(timeArray[1]);
        return timeStamp;
    }

    function transformElement(element, value) {
        element.css({
            '-moz-transform': value,
            '-webkit-transform': value,
            '-ms-transform': value,
            '-o-transform': value,
            'transform': value
        });
    }



</script>
@endsection
