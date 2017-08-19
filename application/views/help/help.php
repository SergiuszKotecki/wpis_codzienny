<?php
$this->title = 'WpisCodzienny - Pomoc';
?>

<div class="row">
    <div class="col-lg-3">
        <?= $this->render('/help/help/menu'); ?>
    </div>
    <div class="col-lg-9">
        <h3>Wprowadzenie w trzech krokach</h3>
        <br />
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Krok 1 - Połącz konto z aplikacją</h4></div>
            <div class="panel-body">
                Do korzystania z aplikacji wymagane jest połączenie jej ze swoim kontem na wykopie. W tym celu należy
                kliknąć w przycisk <strong>"Zaloguj"</strong> lub <strong>"Dołącz do nas"</strong>, który przekieruje Cię na stronę Wykopu.
                <br />
                <br />
                <a href="#" class="thumbnail">
                    <img src="/images/help/join-2.PNG" alt="Połączenie konta z aplikacją" class="img-responsive">
                </a>
                <span style="font-size: 10px; color: red; font-style: italic">
                    (Odznaczenie uprawnień, uniemożliwi poprawne dodawanie wpisów na Mikrobloga)
                </span>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><h4>Krok 2 - Stwórz nowy temat</h4></div>
            <div class="panel-body">
                Po połączeniu swojego konta, stwórz swój pierwszy temat klikając w przycisk <strong>"Dodaj nowy temat"</strong> ("Temat" jest to niejako
                kategoria twoich wpisów, która zawiera ich podstawową konfigurację - godzina wysłania, wołanie użytkowników itp.).
                Zastanów się co chciałbyś wrzucić na Mikrobloga, o której godzinie mają pojawiać się wpisy, czy chcesz wołąć innych użytkowników itp.
                Dla testów, możesz wypełnić pola przykładowymi danymi np.
                <br />
                <br />
                <a href="#" class="thumbnail">
                    <img src="/images/help/add-thread.PNG" alt="Dodanie tematu" class="img-responsive">
                </a>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><h4>Krok 3 - Dodaj swój pierwszy wpis</h4></div>
            <div class="panel-body">
                Jeżeli zapis nowego tematu się powiódł, powinieneś go zobaczyć na liście tematów którymi zarządzasz. Kliknij w ikonę
                <i class="glyphicon glyphicon-menu-hamburger"></i> aby przejść do listy wpisów dla danego tematu, a następnie w
                <strong>"Dodaj nowy wpis"</strong>. Pojawi się formularz, którego dane trafią na Mikrobloga.
                Przykładowe dane mogą wyglądać następująco
                <br />
                <br />
                <a href="#" class="thumbnail">
                    <img src="/images/help/add-thread-row.PNG" alt="Dodanie tematu" class="img-responsive">
                </a>
                Po zapisie, wpis zostanie dodany do bazy danych aplikacji, a o zadanej godzinie(ustawionej w Temacie) wysłany na mikrobloga.
            </div>
        </div>
    </div>
</div>