             
@foreach ($workshops as $workshop)
    <div class='workshop' id='workshop{{ $workshop->id - 1 }}' capacity="{{ $workshop->capacity}}"></div>
        <div class='title showText' id='title{{ $workshop->id - 1 }}' workshop='{{ $workshop->name }}'>{{$workshop->name }}  </div>
        <div class="capacityText title hiddenText" id="capacityText{{ $workshop->id -1}}"></div>
        <div class="locationWorkshop">Deze workshop vind plaats in: <div id="location">#</div></div>
    </div>
@endforeach