@php
    $stt = 1;
@endphp

<div class="timeline">
    <ul>
        @foreach (range(6, 20, 0.5) as $time)
            @php
                $hours = floor($time);
                $minutes = ($time - $hours) * 60;
                $formattedTime = sprintf('%02d:%02d', $hours, $minutes);
            @endphp
            <li><span>{{ $formattedTime }}</span></li>
        @endforeach
    </ul>
</div> <!-- .timeline -->
@php
    $dayOfWeekMap = [
        'Monday' => 1, // Carbon::MONDAY
        'Tuesday' => 2, // Carbon::TUESDAY
        'Wednesday' => 3, // Carbon::WEDNESDAY
        'Thursday' => 4, // Carbon::THURSDAY
        'Friday' => 5, // Carbon::FRIDAY
        'Saturday' => 6, // Carbon::SATURDAY
        'Sunday' => 0, // Carbon::SUNDAY
    ];
@endphp
<div class="events">
    <ul class="wrap">
    @foreach ($dayOfWeekMap as $dayLabel => $dayIndex)
            <li class="events-group">
                <div class="top-info"><span>{{ $dayLabel }}</span></div>
                <ul>
                    @foreach ($lessons->filter(fn($lesson) => \Carbon\Carbon::parse($lesson->Date)->dayOfWeek === $dayIndex) as $lesson)
                        <li class="single-event"
                            data-start="{{ $lesson->lessoninfo->StartTime->format('H:i') }}"
                            data-end="{{ $lesson->lessoninfo2->EndTime->format('H:i') }}"
                            data-content="event-abs-circuit"
                            data-event="event-{{ $stt++ }}"
                            data-class="{{ $lesson->courseclass->studentclass->Code ?? '' }}"
                            data-code="{{ $lesson->courseclass->Code }}"
                            data-room="{{ $lesson->room->Name ?? '' }}"
                            data-period="{{ $lesson->StartLesson }}-{{ $lesson->EndLesson }}">
                            <a href="#">
                                <em class="event-name">{{ $lesson->courseclass->Code }} - {{ $lesson->courseclass->Name }}</em>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</div>

<div class="event-modal">
    <header class="header">
        <div class="content">
            <span class="event-date"></span>
            <h3 class="event-name"></h3>
        </div>
        <div class="header-bg"></div>
    </header>
    <div class="body">
        <div class="event-info"></div>
        <div class="body-bg"></div>
    </div>
    <a href="#0" class="close"></a>
</div>

<div class="cover-layer"></div>
