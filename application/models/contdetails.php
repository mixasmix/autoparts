<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('simpledom/simple_html_dom.php');
/**
 * Класс для работы с контрактными деталями
 */
class Contdetails extends CI_Model {
	public $sql;
	public $errorMessage;
        private $motorland_secret_key='85b66434ed350f9455722d3e135e0fb5';
        
        
        private $motorland_car_model;
        private $motorland_group_parts;
        private $motorland_sort_by;
        private $motorland_order_by=array('1'=>'По убыванию', '0'=>'По возрастанию');
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
                $this->load->helper('getpage');
		$this->load->helper('cookie');
                $this->load->model('Cachemodel','cache');
		$this->sql->query("SET NAMES 'utf8';");
                $this->mtlnd_model_set();
                //$this->mtlnd_group_parts_set();
                //$this->mtlnd_sort_by_set();
                
	}
        /**
         * метод для работы с motorland.by
         * @param string $action Название операции По умолчанию получаем все модели авто
         * @param array $params Массив с параметрами для каждого вида запроса
         * @return mixed 
         */
        public function motorland($action='', $params=false){
           
            
                $result=false;
                if($action=='getmodel'){
                    
                    if(!empty($params['mark_id'])){
                        //var_dump('http://www.motorland.by/api/85b66434ed350f9455722d3e135e0fb5/parts/?_ajax=1&get_brand_models='.$params['mark_id'].'&JsHttpRequest='.time().'-script'); exit;
                       $result=getPage('http://www.xp.by/api/'.$this->motorland_secret_key.'/parts//models/', 'brand_url='.$params['mark_id'], '', 0, 60);
                    }
                    
                }elseif($action=='getmark'){
                    $result=$this->motorland_car_model;
                }elseif($action=='getpartsname'){
                    $result=$this->motorland_group_parts;
                }elseif($action=='getsotrby'){
                    $result=$this->motorland_sort_by;
                }elseif($action=='getorderby'){
                    $result=$this->motorland_order_by;
                }elseif($action=='getoffers'){
                    $query="http://www.xp.by/api/85b66434ed350f9455722d3e135e0fb5/parts/?_ajax=1";
                    $query.='&filter[auto_brand]='.$params['id_mark'];
                    $query.='&filter[auto_model]='.$params['id_model'];
                    $query.='&filter[part_type]='.$params['id_parts_group'];
                    $query.='&filter[article]='.$params['artikul'];
                    $query.='&filter[sort]='.$params['id_sort_by'];
                    $query.='&filter[sort_dir]='.$params['id_order_by'];
                    $query.='&get_parts=1&pg='.$params['pagenum'];
                    $query.='&JsHttpRequest='.time().'-script';
                    
                    $responce=  getPage($query, '', '', 0, 60);
                    $responce=  str_replace('JsHttpRequest.dataReady', '', $responce);
                    $responce=  str_replace('(', '[', $responce);
                    $responce=  str_replace(')', ']', $responce);
                    $responce=  str_replace("['", "('", $responce);
                    $responce=  str_replace("']", "')", $responce);
                    $obj=json_decode($responce);
                    //var_dump($responce); exit;
                   
                        $text=$this->htmlToArray($obj[0]->text, 20);
                    /*}else{
                        $text['text']='<p>Извините, возникли некторые неполадки, попробуйте повторить запрос через некторое время</p>';
                    }*/
                    //$text['text']=$obj[0]->text;
                    $text['text']=  str_replace('erpvaultUcpSA.getParts(', 'pageNav(', $text['text']);
                    $text['text']=  str_replace('Контактный телефон', 'Отправить запрос', $text['text']);
                    $text['text']=  str_replace('erpvaultUcpSA.showImages(', 'showImages(', $text['text']);
                    $result=json_encode(array('text'=>$text['text']));
                    
                }elseif($action=='showimages'){
                   
                    $query='http://www.xp.by/api/85b66434ed350f9455722d3e135e0fb5/parts/?_ajax=1&get_part_images='.$params['img_id'].'&JsHttpRequest='.time().'-script';
                    $responce=  getPage($query);
                    $responce=  str_replace('JsHttpRequest.dataReady', '', $responce);
                    $responce=  str_replace('(', '[', $responce);
                    $responce=  str_replace(')', ']', $responce);
                    
                    $obj=json_decode($responce);
                   
                    $images=$obj[0]->js->images; 
                    
                    $arr_link=array();
                    foreach($images as $img){
                        $link='http://www.xp.by/api/85b66434ed350f9455722d3e135e0fb5/parts/';
                        $link.=$img->data_id.'/images/fullsize/';
                        $link.=$img->n.'.jpg';
                        $a=array();
                        $a['link']=$link;
                        $arr_link[]=$a; 
                        
                    }
                    //var_dump($arr_link); exit;
                    $result=json_encode($arr_link);
                    return $result;
                }
            return $result;
        }
        
        /**
         * Устанавливаем моедли
         */
        
        private function mtlnd_model_set(){
            //$this->motorland_car_model=array('35'=>'*','48'=>'Acura','26'=>'Alfa Romeo','1'=>'Audi','2'=>'BMW','43'=>'Chevrolet','34'=>'Chrysler','20'=>'Citroen','49'=>'Dacia','30'=>'Daewoo','3'=>'DAF','27'=>'Daihatsu','37'=>'Dodge','4'=>'Fiat','5'=>'Ford','6'=>'Honda','55'=>'Hummer','7'=>'Hyundai','50'=>'Infiniti','8'=>'Isuzu','9'=>'Iveco','47'=>'Jaguar','45'=>'Jeep','31'=>'KIA','10'=>'Lancia','39'=>'Land Rover','33'=>'LDV (DAF)','44'=>'Lexus','36'=>'Man','11'=>'Mazda','12'=>'Mercedes','52'=>'Mini','13'=>'Mitsubishi','14'=>'Nissan','15'=>'Opel','16'=>'Peugeot','41'=>'Plymouth','46'=>'Pontiac','54'=>'Porsche','38'=>'Proton','17'=>'Renault','29'=>'Rover','18'=>'Saab','19'=>'Seat','32'=>'Skoda','51'=>'Smart','40'=>'Ssang Yong','21'=>'Subaru','22'=>'Suzuki','23'=>'Toyota','24'=>'Volkswagen','25'=>'Volvo');
            $key=md5('hgwhiobweytgfgggsw'.'http://www.xp.by/api/'.$this->motorland_secret_key.'/parts/form/');
            if(!empty($this->cache->load($key))){
                $params=$this->cache->load($key);
            }else{
                $params=array();
                $responce=getPage('http://www.xp.by/api/'.$this->motorland_secret_key.'/parts/form/', '', '', 0, 60); //запрашиваем страницу с формой
                $responce=str_replace('id="filter[auto_brand_url]"', 'id="filter_auto_brand_url"', $responce); //меняем в ответе id поля для легкого поиска
                $responce=str_replace('id="filter[part_type_url]"', 'id="filter_part_type_url"', $responce); //меняем в ответе id поля для легкого поиска
                $responce=str_replace('id="filter[year_begin]"', 'id="filter_year_begin"', $responce); //меняем в ответе id поля для легкого поиска
                $responce=str_replace('id="filter[year_end]"', 'id="filter_year_end"', $responce); //меняем в ответе id поля для легкого поиска
                $responce=str_replace('id="filter[sort]"', 'id="filter_sort"', $responce); //меняем в ответе id поля для легкого поиска
                $responce=str_replace('id="filter[sort_dir]"', 'id="filter_sort_dir"', $responce); //меняем в ответе id поля для легкого поиска
                
                $html = str_get_html($responce); //скармливаем симпледому
                
                
                /*Выбираем марки авто*/
                /*$select_mark=$html->find('#filter_auto_brand_url option'); //выбираем все option с маркой машины
                $mrk=array();
                foreach ($select_mark as $selmark){ //обходим оптионс цыклом
                    if(empty($selmark->value))
                        continue;
                    $mrk[$selmark->value]=$selmark->innertext;
                }*/
                $params['mrk']=$this->parseForm($html, '#filter_auto_brand_url option');
                /*выбираем название запчасти*/
                $params['partname']=$this->parseForm($html, '#filter_part_type_url option');
                /*выбираем начало года выпуска*/
                $params['startyear']=$this->parseForm($html, '#filter_year_begin option');
                /*год выпуска до*/
                $params['endyear']=$this->parseForm($html, '#filter_year_end option');
                /*сортировка по*/
                $params['sort']=$this->parseForm($html, '#filter_sort option');
                /*направление сортировки*/
                 $params['sortby']=$this->parseForm($html, '#filter_sort_dir option');
                
                
                //$this->cache->save($key, $params, 60*60);
            }
            //var_dump($params); exit;
            $this->motorland_car_model=$params['mrk'];
            $this->motorland_group_parts=$params['partname'];
            $this->motorland_sort_by=$params['sort'];        
        }
        /**
        * метод ищет опшены и возвращает их в виде массива.
         * @param obj $simple_object - объект симпледом
         * @param str $identify - идентификатор селекта
         */
        private function parseForm($simple_object, $identify){
                $select_mark=$simple_object->find($identify); //выбираем все option с маркой машины
                $mrk=array();
                foreach ($select_mark as $selmark){ //обходим оптионс цыклом
                    $mrk[$selmark->value]=$selmark->innertext;
                }
                return $mrk;
        }
        /**
         * Устанавливаем группы зч
         */
        private function mtlnd_group_parts_set(){
            $this->motorland_group_parts=array('238'=>'Аккумулятор (AKБ)','374'=>'Амортизатор капота','1'=>'Амортизатор крышки багажника','175'=>'Амортизатор подвески','286'=>'Аудиотехника','2'=>'Бак топливный','306'=>'Балка подвески задняя','4'=>'Балка подвески передняя (подрамник)','282'=>'Балка под радиатор','5'=>'Бампер','176'=>'Барабан тормозной','385'=>'Бардачок (вещевой ящик)','253'=>'Бачок','130'=>'Блок двигателя (картер)','283'=>'Блок предохранителей','371'=>'Блок реле','100'=>'Блок управления (ЭБУ)','315'=>'Болт колесный (гайка) ','392'=>'Брызговик','102'=>'Вентилятор радиатора','134'=>'Воздуховод','132'=>'Генератор','250'=>'Гидроаккумулятор (груша)','319'=>'Гидротрансформатор АКПП (бублик)','222'=>'Глушитель','133'=>'Головка блока (ГБЦ)','244'=>'Датчик','14'=>'Дверь боковая','13'=>'Дверь задняя (распашная)','15'=>'Дверь раздвижная','349'=>'Двигатель без навесного (ДВС голый)','135'=>'Двигатель (ДВС)','322'=>'Двигатель (ДВС на разборку)','104'=>'Двигатель (насос) омывателя','105'=>'Двигатель отопителя (моторчик печки)','343'=>'Двигатель (привод) фары','224'=>'Двигатель стеклоочистителя (моторчик дворников)','289'=>'Двигатель электролюка','393'=>'Дефлектор обдува салона','333'=>'Диск колeсный алюминиевый R13','180'=>'Диск колeсный алюминиевый R14','184'=>'Диск колeсный алюминиевый R15','332'=>'Диск колeсный алюминиевый R16','348'=>'Диск колeсный алюминиевый R17','350'=>'Диск колeсный алюминиевый R18','362'=>'Диск колесный алюминиевый R19','363'=>'Диск колесный алюминиевый R20','327'=>'Диск колесный обычный R13','181'=>'Диск колесный обычный R14','326'=>'Диск колесный обычный R15','345'=>'Диск колесный обычный R16','346'=>'Диск колесный обычный R17','182'=>'Диск опорный тормозной','136'=>'Диск сцепления','183'=>'Диск тормозной','342'=>'Дисплей компьютера','17'=>'Домкрат','339'=>'Дуги на крышу (рейлинги)','378'=>'Жидкость стеклоомывателя','395'=>'Заглушка накладки на порог','394'=>'Заглушка (решётка) бампера','18'=>'Замок багажника','20'=>'Замок двери','106'=>'Замок зажигания','288'=>'Замок капота','366'=>'Заслонка дроссельная','279'=>'Защита (кожух) ремня ГРМ','22'=>'Защита крыла пластмассовая (подкрылок)','239'=>'Защита моторного отсека (картера ДВС)','23'=>'Зеркало боковое','24'=>'Зеркало салона','137'=>'Измеритель потока воздуха (расходомер)','380'=>'Инструмент','25'=>'Капот','138'=>'Карбюратор','254'=>'Кардан','258'=>'Кардан рулевой','107'=>'Катушка зажигания','396'=>'Клапан воздушный','397'=>'Клапан рециркуляции газов (EGR)','139'=>'Клапан холостого хода','113'=>'Кнопка (выключатель)','261'=>'Кожух вентилятора радиатора (диффузор)','367'=>'Козырек солнцезащитный','140'=>'Коленвал','187'=>'Колесо запасное (таблетка)','142'=>'Коллектор впускной','388'=>'Коллектор выпускной','189'=>'Колодка тормозная','247'=>'Колонка рулевая','191'=>'Колпак колесный','114'=>'Коммутатор зажигания','241'=>'Компрессор кондиционера','341'=>'Компрессор пневмоподвески','115'=>'Компрессор центрального замка','305'=>'Консоль салона (кулисная часть)','143'=>'Корзина (кожух) сцепления','245'=>'Корпус воздушного фильтра','398'=>'Корпус масляного фильтра','399'=>'Корпус топливного фильтра','317'=>'Корректор фар','192'=>'КПП 4 ст.','193'=>'КПП 5 ст.','303'=>'КПП 6-ти','194'=>'КПП-автомат (АКПП)','293'=>'Кран отопителя (печки)','281'=>'Кронштейн (лапа крепления)','29'=>'Крыло','144'=>'Крыльчатка вентилятора (лопасти)','298'=>'Крыша кузова','30'=>'Крышка (дверь) багажника','285'=>'Крышка клапанная ДВС','320'=>'Крышка распределителя зажигания','400'=>'Крюк буксировочный','31'=>'Кулиса КПП','277'=>'Лонжерон кузовной','33'=>'Люк','284'=>'Лючок бензобака','146'=>'Маховик','166'=>'Механизм натяжения ремня, цепи','34'=>'Механизм раздвижной двери','299'=>'Механизм стеклоочистителя (трапеция дворников)','291'=>'Модуль (блок) ABS','35'=>'Молдинг (накладка кузовная)','147'=>'Моновпрыск','196'=>'Мост (ведущий)','148'=>'Муфта вентилятора (вискомуфта)','197'=>'Муфта кардана','324'=>'Нагнетатель воздуха (насос продувки)','243'=>'Накладка декоративная','389'=>'Накладка декоративная (бленда)','373'=>'Накладка декоративная (дождевик)','390'=>'Накладка декоративная (на ДВС)','391'=>'Накладка декоративная (на порог)','321'=>'Направляющая шторки багажника (салазки)','149'=>'Насос вакуумный','150'=>'Насос водяной (помпа)','151'=>'Насос гидроусилителя руля (ГУР)','152'=>'Насос масляный','153'=>'Насос топливный механический','234'=>'Насос топливный ручной (подкачка)','240'=>'Насос топливный электрический','386'=>'Насос электрический усилителя руля','401'=>'Обшивка салона','219'=>'Опора амортизатора верхняя (чашка)','236'=>'Отопитель в сборе (печка)','26'=>'Панель передняя салона (торпеда)','154'=>'Патрубок (трубопровод, шланг)','364'=>'Педаль','287'=>'Пепельница','119'=>'Переключатель дворников (стеклоочистителя)','120'=>'Переключатель отопителя (печки)','372'=>'Переключатель поворотов','121'=>'Переключатель поворотов и дворников (стрекоза)','122'=>'Переключатель света','290'=>'Петля двери','375'=>'Петля капота','382'=>'Петля крышки багажника','402'=>'Пневмоподушка','88'=>'Поворот','155'=>'Поддон','384'=>'Подлокотник','403'=>'Подножка','89'=>'Подсветка номера','266'=>'Подушка безопасности (Airbag)','40'=>'Подушка крепления двигателя','309'=>'Подушка крепления КПП','203'=>'Подшипник выжимной','42'=>'Полка багажника','204'=>'Полуось (приводной вал, шрус)','156'=>'Поршень','205'=>'Привод спидометра КПП','43'=>'Прицепное устройство (фаркоп)','44'=>'Пробка бензобака','313'=>'Пробка маслозаливная','45'=>'Пробка расширительного бачка','159'=>'Провод высоковольтный','228'=>'Прочая запчасть','206'=>'Пружина подвески','278'=>'Радиатор интеркулера','221'=>'Радиатор кондиционера','47'=>'Радиатор масляный','46'=>'Радиатор (основной)','48'=>'Радиатор отопителя (печки)','377'=>'Раздаточный редуктор КПП (раздатка)','369'=>'Рамка капота','229'=>'Рамка передняя (телевизор)','404'=>'Рамка под магнитолу','160'=>'Распредвал','161'=>'Распределитель впрыска (инжектор)','162'=>'Распределитель зажигания (трамблёр)','271'=>'Распределитель тормозной силы','163'=>'Регулятор давления топлива','207'=>'Редуктор моста','94'=>'Редуктор рулевой','95'=>'Рейка рулевая без г/у','96'=>'Рейка рулевая с г/у','311'=>'Реле бензонасоса','124'=>'Реле накала','125'=>'Реле прочее','383'=>'Ремень безопасности','208'=>'Рессора','51'=>'Решетка радиатора','97'=>'Руль','19'=>'Ручка бардачка','55'=>'Ручка двери нaружная','56'=>'Ручка двери салона','405'=>'Ручка крышки багажника','301'=>'Ручка открывания капота','57'=>'Ручка стеклоподъемника','210'=>'Рычаг подвески','265'=>'Рычаг ручного тормоза (ручника)','312'=>'Сигнал (клаксон)','59'=>'Сидение','338'=>'Сопротивление отопителя (моторчика печки)','318'=>'Спойлер','256'=>'Стабилизатор подвески (поперечной устойчивости)','168'=>'Стартер','61'=>'Стекло боковой двери','62'=>'Стекло заднее','242'=>'Стекло кузовное боковое','65'=>'Стекло лобовое','67'=>'Стеклоподъемник механический','68'=>'Стеклоподъемник электрический','80'=>'Стекло форточки двери','213'=>'Ступица (кулак, цапфа)','214'=>'Суппорт','272'=>'Термостат','169'=>'ТНВД','69'=>'Торсион крышки багажника','215'=>'Торсион подвески','310'=>'Трос газа','71'=>'Трос двери','72'=>'Трос капота','365'=>'Трос кулисы КПП','73'=>'Трос ручника','74'=>'Трос спидометра','75'=>'Трос сцепления','295'=>'Труба приёмная глушителя','370'=>'Трубка кондиционера','170'=>'Трубка ТНВД','171'=>'Турбина','276'=>'Тяга','216'=>'Тяга рулевая','246'=>'Узел педальный (блок педалей)','76'=>'Уплотнитель','78'=>'Усилитель бампера','79'=>'Усилитель тормозов вакуумный','77'=>'Ус под фару (ресничка)','406'=>'Ус под фонарь (ресничка)','90'=>'Фара (передняя)','91'=>'Фара противотуманная (галогенка)','409'=>'Фильтр новый','92'=>'Фонарь (задний)','93'=>'Фонарь крышки багажника','344'=>'Фонарь противотуманный (габаритный)','368'=>'Фонарь салона (плафон)','172'=>'Форсунка','82'=>'Цилиндр сцепления главный','217'=>'Цилиндр сцепления рабочий','83'=>'Цилиндр тормозной главный','307'=>'Цилиндр тормозной рабочий','323'=>'Часть кузова (вырезанный элемент)','129'=>'Часы','262'=>'Шатун','297'=>'Шестерня','376'=>'Шина (б/у)','223'=>'Шина (б/у) парная ','381'=>'Шина (новая) зимняя','328'=>'Шина (новая) летняя','173'=>'Шкив','314'=>'Шланг ГУР','407'=>'Шлейф руля','84'=>'Шторка багажника','85'=>'Щеткодержатель','231'=>'Щиток приборов (приборная панель)','86'=>'Эмблема','87'=>'Юбка бампера нижняя');
           
            
        }
        private function mtlnd_sort_by_set(){
            $this->motorland_sort_by=array( 'rank'=>'По релевантности',  'part_type_name'=>'По названию запчасти',  'auto_brand_name'=>'По марке',  'auto_model_name'=>'По модели',  'year'=>'По году выпуска',  'engine_capacity_name'=>'По объему двигателя',  'engine_type_name'=>'По типу двигателя',  'engine_detail_name'=>'По особенностям двигателя',  'direction_name'=>'По направлению',  'side_name'=>'По стороне',  'auto_body_name'=>'По типу кузова',  'color_name'=>'По цвету',  'constrn'=>'По конструкционному номеру',  'comments'=>'По примечаниям',  'price_us'=>'По цене');
        }
        
        public function htmlToArray($str, $procents, $backet_code=''){
            $html = str_get_html($str);
            $table=@$html->find('.erpvault_ucp_parts_table', 0);//мы нашли таблицу основную
            $procent=100+$procents;
            if(!empty($table)){
                $tabrow=$table->find('tr');
                /*$th=$table->find('th');*/
                $arr=array();
                foreach($tabrow as $tr){
                    $td=$tr->find('td');
                    if(!empty($td)){
                        $counter=count($td);
                        $a=array();
                        $td[$counter-1]->innertext='<a href="#" class="addQuery">Отправить запрос</a>';
                        $td[$counter-2]->price=(intval(str_replace(' ', '', $td[$counter-2]->plaintext)));
                        $td[$counter-2]->innertext=(intval(str_replace(' ', '', $td[$counter-2]->plaintext))/100)*$procent;
                        
                        
                    }
                }
                
            }
            $pagination=false;
            return array('text'=>$html->save(), 'pagination'=>$pagination);
        }
}