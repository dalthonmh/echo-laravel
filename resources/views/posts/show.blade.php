@extends('layouts.app')

@section('content')
  <div class="container">
    <h1>{{ $post->title }}</h1>
    {{ $post->updated_at->toFormattedDateString() }}
    @if ($post->published)
      <span class="label label-success" style="margin-left:15px;">Published</span>
    @else
      <span class="label label-default" style="margin-left:15px;">Draft</span>
    @endif
    <hr />
    {{-- <p>{{ Auth::user()->api_token }}</p>
    <hr> --}}
    <p class="lead">
      {{ $post->content }}
    </p>
    <hr />

    <h3>Comments:</h3>
    <div style="margin-bottom:50px;" v-if="user">
      <textarea class="form-control" rows="3" name="body" placeholder="Leave a comment" v-model="commentBox"></textarea>
      <button 
        class="btn btn-success" 
        style="margin-top:10px" 
        v-on:click.prevent="postComment" >Save Comment</button>
    </div>
    <div v-else>
      para comentar debe 
      <a href="/login">iniciar sesión</a>
    </div>

    <div class="media" style="margin-top:20px;" v-for="comment in comments">
      <div class="media-left">
        <a href="#">
          <img class="media-object" src="http://placeimg.com/80/80" alt="...">
        </a>
      </div>
      <div class="media-body" >
        <h4 class="media-heading">@{{ comment.user.name }} said...</h4>
        <p>
          @{{ comment.body }}
        </p>
        <span style="color: #aaa;">on @{{ comment.updated_at }}</span>
      </div>
    </div>
  </div>
@endsection


@section('scripts')
<script>

  const app = new Vue({
      el: '#app',
      data: {
        comments: {},
        commentBox: '',
        post: {!! $post->toJson() !!},
        user: {!! Auth::check() ? Auth::user()->toJson() : 'null' !!}
      },
      mounted(){
        this.getComments();
        this.listen();
      },
      methods: {
        getComments(){
          axios.get(`/api/posts/${this.post.id}/comments`)
            .then((response) => {
                this.comments = response.data
            })
            .catch(function (error){
              console.log(error);
            });
        },
        postComment(){
          axios.post(`/api/posts/${this.post.id}/comment`, {
            api_token: this.user.api_token,
            body: this.commentBox
          })
          .then((response) => {
            this.comments.unshift(response.data);
            this.commentBox = '';
          })
          .catch(function (error) {
            console.log(error);
          });
        },
        listen(){
            Echo.channel('post.'+this.post.id)
              .listen('NewComment', (comment) => {
                  this.comments.unshift(comment);
              })
        }
      }
  });
  
</script>
@endsection
