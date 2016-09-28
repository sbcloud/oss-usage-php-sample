<h1>bucket: {{ bucket.bucketName }}</h1>
<form enctype="multipart/form-data" action="upload" method="POST">
  <input name="newfile" type="file" />
  <input type="submit" value="Upload" />
</form>
{% for content in contents %}
  <li>
    {{ content.name }}
    <a href="/download?file_name={{ content.name }}" target="_blank">download</a>
    <a href="/delete?file_name={{ content.name }}">delete</a>
  </li>
{% endfor %}
