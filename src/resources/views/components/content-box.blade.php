<div {{ $attributes }}>
    <div style="align-self: stretch; padding: 19px 41px;background: #323232; flex-direction: column; justify-content: center; align-items: flex-start; gap: 10px; display: flex">
        <div style="align-self: stretch; color: white; font-size: 18px; font-family: Ubuntu; font-weight: 700; text-transform: uppercase; word-wrap: break-word">
            {{ $title }}
        </div>
    </div>
    <div style="align-self: stretch; flex: 1 1 0; background: white; border-left: 3px #D9D9D9 solid; border-right: 3px #D9D9D9 solid; border-bottom: 3px #D9D9D9 solid">
        {{ $slot }}
    </div>
</div>
