<?php

namespace Http\Controllers;

use App\Models\Professional;
use App\Models\User;
use App\Models\Verification;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Mockery;
use Mockery\MockInterface;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Tests\TestCase;
use function fake;
use function route;

class UserControllerTest extends TestCase
{
    /**
     * @throws UnknownProperties
     */
    public function test_should_store_professional()
    {
        /** @var User $user */
        $user = $this->app->make(Authenticatable::class);
        $user->verification = new Verification([
            'expires_at' => Carbon::tomorrow()->format('Y-m-d H:i:s'),
            'code' => 123456,
        ]);
        $user->id = 'test-user';

        $file = UploadedFile::fake()->image('temp.jpeg');

        $testResponse = $this->actingAs($user)->post(route('professional.store'), [
            'phone_number' => fake('pt_BR')->phoneNumber(),
            'taxpayer_id' => fake('pt_BR')->cpf(),
            'profile_photo' => $file,
            'verification_code' => 123456,
        ]);

        self::assertEquals(302, $testResponse->getStatusCode());
        self::assertEquals(route('dashboard'), $testResponse->headers->get('Location'));
    }

    protected function setUp(): void
    {
        parent::setUp();
        /** @var User $user */
        $user = Mockery::mock(User::class, function (MockInterface $mock) {
            $mock->shouldReceive('save')->andReturn(true);
        })->makePartial();
        $this->instance(Authenticatable::class, $user);

        $professional = Mockery::mock(Professional::class, function (MockInterface $mock) {
            $mock->shouldReceive('saveOrFail')->andReturn(true);
        })->makePartial();
        $this->instance(Professional::class, $professional);

        $uploadedFile = Mockery::mock(UploadedFile::class, function (MockInterface $mock) {
            $mock->shouldReceive('storePubliclyAs')->andReturn(true);
        })->makePartial();
        $this->instance(UploadedFile::class, $uploadedFile);
    }
}
