<div>
    <!-- It is quality rather than quantity that matters. - Lucius Annaeus Seneca -->
    <section class=" subscribe-style-one home-two" style="background-image: url({{path()}}images/subscribe-bg-1-1.jpg); background-attachment: fixed; background-position: center center;">
        <div class="inner">
           <div class="thm-container">
              <div class="row">
                 <div class="col-md-6">
                    <div class="subscribe-content">
                       <h3> {{__('news_letter_heading')}} </h3>
                       <p> {{__('news_letter_text')}} </p>
                    </div>
                 </div>
                 <div class="col-md-6">
                    <form onsubmit="return saveNewsletter(event);" accept-charset="utf-8" class="subscribe-form clearfix">
                       <input type="hidden" id="uri2" name="uri" value="">
                       <input type="text" placeholder="{{__('your_email_address')}}" name="email"/>
                       <button type="submit"> {{__('sign_up')}} <i class="fa fa-angle-double-right"></i></button>
                    </form>
                    <div class="text-white text-center subscribe-message"></div>
                 </div>
              </div>
           </div>
        </div>
     </section>
@section('scripts')
   <script>
      var newsLetterSave = "{{ route('newsletter.store') }}";
      function saveNewsletter(e) {
         e.preventDefault();
         var formData = $('.subscribe-form').serialize();
         $.ajax(newsLetterSave, {
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response, status, xhr) {
               $(".subscribe-message").html(response.message);
               $('.subscribe-form')[0].reset();
            },
            error: function(jqXhr, textStatus, errorMessage) {
               // $('p').append('Error' + errorMessage);
            }
         });
      }
   </script>
@endsection
</div>