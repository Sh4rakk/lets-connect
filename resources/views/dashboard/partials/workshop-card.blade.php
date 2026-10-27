{{-- Workshop kaartje op desktop. Staat in een x-for met "card" (en "index" in de lijst, "round" in een ronde). --}}
<div class="workshop"
     :id="'workshop' + card.id"
     :draggable="!registrationsClosed"
     @dragstart="startDrag($event, card.id)"
     @dragend="draggingId = null"
     @mouseenter="hoverId = card.id"
     @mouseleave="hoverId = null"
     :style="registrationsClosed ? 'opacity: 0.5; position: relative;' : ''"
     @if(!$inRound)
         :class="{ 'tutorial-focus': $store.tutorial.is(3) && index === 0 }"
     @endif
>
    <template x-if="registrationsClosed">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 32px; z-index: 10;">🔒</div>
    </template>

    <div class="popup" :id="'popup' + card.id" draggable="false" :style="openInfoId === card.id ? 'display: flex;' : ''">
        <button class="close" @click.stop="closeInfo()">x</button>
        <a href="https://xerte.deltion.nl/play.php?template_id=8708#programma" class="popup-header" target="_blank">Klik <span class="highlight">hier</span> voor meer informatie</a>
        <div class="description">
            <div class="descriptionText" x-text="card.full_description"></div>
            <div class="descriptionImage"><img :src="card.image_url" alt="Workshop afbeelding"></div>
        </div>
    </div>

    <div class="workshop-header">
        <div class="info"
             :id="'info' + card.id"
             tabindex="0"
             @click.stop="toggleInfo(card.id)"
             @keydown.enter.prevent="toggleInfo(card.id)"
             @if(!$inRound)
                 :class="{ 'tutorial-focus tutorial-info-highlight': $store.tutorial.is(1) && index === 0 }"
             @endif
        >i</div>
        <div class="title showText" :id="'title' + card.id" x-text="card.name"></div>
    </div>

    <div class="capacityText" :id="'capacityText' + card.id" :class="{ 'whiteLoader': !capacitiesLoaded, 'hidden': hoverId === card.id }">
        <template x-for="line in capacityLines(card.id)" :key="line">
            <p x-text="line"></p>
        </template>
    </div>

    <div class="locationWorkshop" :class="{ 'hidden': hoverId !== card.id }">
        Deze workshop vind plaats in: <div x-text="card.location"></div>
    </div>

    @if($inRound)
        <button class="close-button"
                @click.stop="removeFromRound(round)"
                :disabled="$store.tutorial.is(3)"
                :class="{ 'tutorial-focus': $store.tutorial.is(4) }"
        >×</button>
    @endif
</div>
