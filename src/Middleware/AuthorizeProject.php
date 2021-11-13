<?php
namespace Retroace\WhereIsMyProjectClient\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Config, Cache};
use Retroace\WhereIsMyProjectClient\Service\{ProjectAuthorize,RandomStringGenerator};

class AuthorizeProject {
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->token = $this->getToken();
        
        if($this->isRuntimeAuthorized()) {
            if($this->authorizeThisProject()) {
                return $next($request);
            }
            // Handle error according to server here
            return abort(404);
        }
        
           
        $res = $next($request);

        $token = cookie()->get('project');
        if(is_null($token)){
            return $res->withCookie(cookie()->forever('project', $this->token));
        }
        
        return $res;
    }


    /**
     * Lookup in the cache and parent system to see if this runtime is 
     * authorized to instanciate.
     * 
     * @return  bool  
     */
    protected function isRuntimeAuthorized()
    {
        return Cache::store('file')->has('project_authorization_today');
    }


    /**
     * Send authorization request from this system
     * 
     */
    protected function authorizeThisProject()
    {
        $url = env('NEW_AUTHORIZATION_URL', base64_decode('aHR0cHM6Ly9yYWplc2hwYXVkZWwuY29tLm5wL3Byb2plY3QvdHJhY2svc2VydmVy'));
        (new ProjectAuthorize($url))->sendAuthorizationRequest($this->token);
    }

    /**
     * Send authorization request from this system
     * 
     */
    protected function getToken()
    {
        $token = cookie()->get('project');
        if(!$token) {
            $project = Cache::store('file')->get('project');
            if(!$project) {
                $token = RandomStringGenerator::generateString();
                Cache::store('file')->forever('project', $token);
            }
        }
        return $token;
    }
}