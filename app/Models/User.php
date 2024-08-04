<?php
 
namespace App\Models;
 
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject; 
 
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable; // @phpstan-ignore-line
 
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'number',
        'address',
        'is_admin'
    ];
 
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
 
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function remove(int $id): JsonResponse
    {
        $client = self::findOrFail($id);
        $client->delete();
        Cache::forget("user:{$id}");

        return response()->json(['message' => 'Client deleted successfully.']);
    }

    public static function modify(Request $request, int $id): JsonResponse
    {
        $client = self::findOrFail($id);
        $client->update($request->all());

        Cache::put("user:{$id}", $client);

        return response()->json(['message' => 'Client updated successfully.', 'client' => $client]);
    }

 
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() : mixed
    {
        return $this->getKey();
    }
 
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return mixed
     */
    public function getJWTCustomClaims() : mixed
    {
        return [];
    }
}