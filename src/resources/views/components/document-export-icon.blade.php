<div>
    <a href="/trips/{{ $id }}/export?fileType={{ $docType }}" class="text-decoration-none text-dark">
        <i class="fa-solid fa-file-pdf fa-2x mb-1"></i>
        <p>{{ $docType->fileName() }}</p>
    </a>
</div>
