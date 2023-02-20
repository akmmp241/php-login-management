<?phpnamespace Akmalmp\BelajarPhpMvc\Controller;use Akmalmp\BelajarPhpMvc\App\View;use Akmalmp\BelajarPhpMvc\Config\Database;use Akmalmp\BelajarPhpMvc\Repository\SessionRepository;use Akmalmp\BelajarPhpMvc\Repository\UserRepository;use Akmalmp\BelajarPhpMvc\Service\SessionService;class HomeController{    private SessionService $sessionService;    public function __construct()    {        $connection = Database::getConnection();        $sessionRepository = new SessionRepository($connection);        $userRepository = new UserRepository($connection);        $this->sessionService = new SessionService(sessionRepository: $sessionRepository, userRepository: $userRepository);    }    function index()    {        $user = $this->sessionService->current();        if ($user == null) {            View::render('Home/index', [                'title' => 'Login Management'            ]);        } else {            View::render('Home/dashboard', [                'title' => 'Dashboard',                'user' =>  [                    'name' => $user->getName()                ]            ]);        }    }}