@php $mkey = basename($url); @endphp
@if($type === 'image')
<a class="dia-media" href="javascript:void(0)" onclick="diaOpenImg(this)"><img class="dia-media-img" data-mkey="{{ $mkey }}" data-msrc="{{ $url }}" alt="foto"></a>
@elseif($type === 'audio')
<audio class="dia-media-audio" controls preload="none" data-mkey="{{ $mkey }}" data-msrc="{{ $url }}"></audio>
@elseif($type === 'video')
<video class="dia-media-video" controls preload="metadata" data-mkey="{{ $mkey }}" data-msrc="{{ $url }}"></video>
@endif
