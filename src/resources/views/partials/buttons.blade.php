
<div class="d-flex gap-3">
    <a href="/edit/{{ Crypt::encrypt($id)}}" class="text-secondary">
        <i class="fa-regular fa-pen-to-square"></i>
    </a>
    <a href="/delete/{{ Crypt::encrypt($id)}}" class="text-danger">
        <i class="fa-regular fa-trash-can"></i>
    </a>
</div>