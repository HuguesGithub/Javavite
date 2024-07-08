<section class="content col-12 col-lg-4 offset-lg-4 pt-5 %1$s" style="position: absolute;">
  <div class="container-fluid">
      <div class="alert alert-warning alert-dismissible fade show">
        <div class="card-header text-center">
        <strong>Erreur</strong>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

        </div>
        <div class="card-body">%2$s</div>
      </div>
  </div>
</section>

<section class="content file-panel col-12">
  <div class="container-fluid">%3$s</div>
</section>

<section class="content file-panel col-12">
  <div class="container-fluid">%4$s</div>
</section>

<section class="content file-panel col-12 col-lg-4 offset-lg-4 d-none">
  <div class="container-fluid">
    <div class="file-box" style="width: inherit;">
      <div class="card text-bg-secondary">
        <div class="card-header text-center"><strong>Fichier à analyser</strong></div>
        <div class="card-body">
          <form action="/" method="post" enctype="multipart/form-data">
            <div class="input-group mb-3">
                <label class="input-group-text" for="fileName">Upload</label>
                <input class="form-control" type="file" id="fileName" value="fileName">
            </div>
            <div class="row">
              <div class="col-8 d-none d-xl-block"></div>
              <div class="col-xs-12 col-lg-4">
              <input type="hidden" name="formName" value="replayAnalysis"/>
                <button type="submit" class="btn btn-secondary btn-block">Valider</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
  <!--/. container-fluid -->
</section>