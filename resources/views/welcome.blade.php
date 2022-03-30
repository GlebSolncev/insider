<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/bootstrap-theme.css" rel="stylesheet">

</head>
<body>
<div class="container">

    <div class="jumbotron">

        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12 text-center" style="font-size:15pt">League Table</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">Teams</div>
                        <div class="col-md-1">PTS</div>
                        <div class="col-md-1">P</div>
                        <div class="col-md-1">W</div>
                        <div class="col-md-1">D</div>
                        <div class="col-md-1">L</div>
                        <div class="col-md-1">GD</div>
                    </div>

                    <div class="list-clubs"></div>


                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12 text-center fs-6" style="font-size:15pt">Match Results</div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center info-num-week"># Week Match Result</div>
                    </div>
                    <div class="row weeks"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <button class="play-all">Play All</button>
                </div>
                <div class="col-md-8"></div>
                <div class="col-md-2">
                    <button class="next-week" data-my-week="0">Next Week</button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-info btn-sm settings" style="margin-top: 10px" data-toggle="modal"
                data-target="#myModal">Open Modal
        </button>
        <!-- Button trigger modal -->

    </div>

</div>
<style>
    .list-settings > li {
        padding: 5px;
        list-style-type: none;
        cursor: pointer;
    }

    .list-settings > li.active {
        background: #2aabd2;
    }

    .list-settings > li:hover {
        background: #2aabd2;
    }

    .example-club {
        display: none;
    }
</style>

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Teams list</h4>
                <button type="button" class="btn btn-primary save-settings" data-dismiss="modal">Save</button>
            </div>
            <div class="modal-body">
                <div class="list-setting">
                    <p class="loader-settings">Loading...</p>
                    <div class="body-settings">
                        <p class="header-settings text-center"></p>
                        <ul class="list-settings"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div>
        </div>

    </div>
</div>

<script src="{{ asset('js/jquery-3.3.1.slim.min.js') }}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"></script>
<script src="/js/bootstrap.min.js" crossorigin="anonymous"></script>
<!-- Button trigger modal -->
</body>

<script>
    $(document).ready(function () {
        accessBtn(false)
        html = $('html')
        LEAGUE_ID = null

        $('.settings').click(function () {
            $.ajax({
                type: "GET",
                url: "{{ route('club.index') }}",
                data: {
                }
            }).done(function (items) {
                $('.loader-settings').html("")
                lsettings = $('.list-settings')
                body = $('.body-settings')
                header = $('.header-settings')
                for (i = 0; i < items.length; i++) {
                    header.html("Teams list")
                    item = items[i]
                    lsettings.append('<li class="item-list-settings" data-id="' + item.id + '">' + (i + 1) + '. ' + item.name + '</li>')
                }

                html = $('html')
            });

        });

        $('.list-settings').on('click', 'li.item-list-settings', function () {
            $(this).addClass('active')
            length = $('li.item-list-settings.active').length
            num = (length / 2).toFixed(0) - 1
            id = $(this).attr('data-id');

            $(this).attr('data-id', id);
            $(this).attr('data-group', num);
        });
        $('.list-settings').on('click', 'li.item-list-settings.active', function () {
            $(this).removeClass('active').removeAttr('data-group');
        });

        $('button.save-settings').on('click', function () {
            activeList = $('li.item-list-settings.active')
            ids = Array();


            $('li.item-list-settings.active').each(function (i, el) {
                group = $(el).data('group');
                ids[group] = []
            }).each(function (i, el) {
                group = $(el).data('group');
                if (ids[group].length) {
                    ids[group][ids[group].length] = $(el).data('id')
                } else {
                    ids[group][0] = $(el).data('id')
                }
            })
            $('.list-clubs').text("Leading....")
            $.ajax({
                type: "POST",
                url: "{{ route('league.store') }}",
                data: {
                    groups: ids,
                    _token: '{{ csrf_token() }}'
                }
            }).done(function (res) {
                this.LEAGUE_ID = res.league_id

                list = $('.list-clubs').html("")

                Object.keys(res.clubs).forEach(function (id) {
                    name = res.clubs[id]

                    list.append($('<div class="row" data-item="' + id + '">' +
                        '<div class="col-md-6 title-team">' + name + '</div>' +
                        '<div class="col-md-1 pts-team">0</div>' +
                        '<div class="col-md-1 p-team">0</div>' +
                        '<div class="col-md-1 w-team">0</div>' +
                        '<div class="col-md-1 d-team">0</div>' +
                        '<div class="col-md-1 l-team">0</div>' +
                        '<div class="col-md-1 gd-team">0</div>' +
                        '</div>'))
                });

                $('.weeks').addClass('text-center').text('Enter Next Week or Play All')
                $('body').attr('data-league-id', res.league_id)
                $('.next-week').attr('data-max-week', res.max_weeks)
                accessBtn(true)
            });

        });

        $('html').on('click', 'button.next-week', function () {
            $('.weeks').addClass('text-center').text('Loading...')
            myWeek = $(this).attr('data-my-week')
            maxWeek = $(this).attr('data-max-week')

            $.ajax({
                type: "POST",
                url: "{{ route('league.next-week') }}",
                data: {
                    week: myWeek,
                    league_id: $('html body').attr('data-league-id'),
                    _token: '{{ csrf_token() }}'
                }
            }).done(function (items) {
                $('.weeks').addClass('text-center').text('')
                updateData(items.match_games)
            })

            nextWeek = parseInt(myWeek) + 1
            console.log('>>>>', myWeek, nextWeek, maxWeek)
            if (nextWeek <= maxWeek) {
                $('.info-num-week').text(nextWeek + "# Week Match Result")
                $(this).attr('data-my-week', nextWeek)
            } else {
                accessBtn(false)
            }
        });

        $('html').on('click', 'button.play-all', function () {
            $('.weeks').addClass('text-center').text("")

            $.ajax({
                type: "POST",
                url: "{{ route('league.play-all') }}",
                data: {
                    league_id: $('html body').attr('data-league-id'),
                    _token: '{{ csrf_token() }}'
                }
            }).done(function (items) {
                for (className of ['pts-team', 'p-team', 'w-team', 'd-team', 'l-team', 'gd-team']) {
                    $('.' + className).text('0')
                }

                updateData(items.match_games)
                $('.info-num-week').text(items.match_games.length + "# Week Match Result")
                accessBtn(false)
            });
        });

        function accessBtn(access = true) {
            if (access) {
                $('.next-week').removeAttr('disabled')
                $('.play-all').removeAttr('disabled')
            } else {
                $('.next-week').attr('disabled', 'disabled')
                $('.play-all').attr('disabled', 'disabled')
            }
        }

        function updateData(match_games) {
            teams = []
            console.log(match_games)
            for (item of match_games) {
                fNameClub = item.games[0].club.name
                fGoals = item.games[0].goals
                sNameClub = item.games[1].club.name
                sGoals = item.games[1].goals

                $('.weeks').append(
                    $('<div class="col-md-12 match-info" data-id="' + item.id + '"><div class="col-md-5 text-right first-club-name">' + fNameClub + '</div>' +
                        '<div class="col-md-2"><span class="first-club-goals">' + fGoals + '</span>-<span class="second-club-goals">' + sGoals + '</span></div>' +
                        '<div class="col-md-5 text-left second-club-name">' + sNameClub + '</div></div>')
                );


                firstTeamId = item.games[0].club.id
                secondTeamId = item.games[1].club.id

                if (teams[firstTeamId]) {
                    teams[firstTeamId] += fGoals - sGoals
                } else {
                    teams[firstTeamId] = fGoals - sGoals
                }
                if (teams[secondTeamId]) {
                    teams[secondTeamId] += sGoals - fGoals
                } else {
                    teams[secondTeamId] = sGoals - fGoals
                }

                item = $('.list-clubs>.row[data-item="' + firstTeamId + '"]>.p-team')
                item.text(parseInt(item.text()) + 1)
                item = $('.list-clubs>.row[data-item="' + secondTeamId + '"]>.p-team')
                item.text(parseInt(item.text()) + 1)
                if (fGoals == sGoals) {
                    item = $('.list-clubs>.row[data-item="' + firstTeamId + '"]>.d-team')
                    item.text(parseInt(item.text()) + 1)
                    item = $('.list-clubs>.row[data-item="' + secondTeamId + '"]>.d-team')
                    item.text(parseInt(item.text()) + 1)
                }

                if (fGoals > sGoals) {
                    item = $('.list-clubs>.row[data-item="' + firstTeamId + '"]>.w-team')
                    item.text(parseInt(item.text()) + 1)
                    item = $('.list-clubs>.row[data-item="' + secondTeamId + '"]>.l-team')
                    item.text(parseInt(item.text()) + 1)
                }

                if (fGoals < sGoals) {
                    item = $('.list-clubs>.row[data-item="' + firstTeamId + '"]>.l-team')
                    item.text(parseInt(item.text()) + 1)
                    item = $('.list-clubs>.row[data-item="' + secondTeamId + '"]>.w-team')
                    item.text(parseInt(item.text()) + 1)
                }


            }

            for (id in teams) {
                console.log(id, teams[id])
                gd = teams[id]
                if (gd > 0) {
                    gd = '+' + gd
                }
                $('.list-clubs>.row[data-item="' + id + '"]>.gd-team').text(gd)
            }

            for (item of match_games) {
                firstTeamId = item.games[0].club.id
                ptsW = parseInt($('.list-clubs>.row[data-item="' + firstTeamId + '"]>.w-team').text()) * 3
                ptsD = parseInt($('.list-clubs>.row[data-item="' + firstTeamId + '"]>.d-team').text()) * 1
                ptsGD = parseInt($('.list-clubs>.row[data-item="' + firstTeamId + '"]>.gd-team').text()) * 1
                $('.list-clubs>.row[data-item="' + firstTeamId + '"]>.pts-team').text(ptsW + ptsD + ptsGD)

                secondTeamId = item.games[1].club.id
                ptsW = parseInt($('.list-clubs>.row[data-item="' + secondTeamId + '"]>.w-team').text()) * 3
                ptsD = parseInt($('.list-clubs>.row[data-item="' + secondTeamId + '"]>.d-team').text()) * 1
                ptsGD = parseInt($('.list-clubs>.row[data-item="' + secondTeamId + '"]>.gd-team').text())


                $('.list-clubs>.row[data-item="' + secondTeamId + '"]>.pts-team').text(ptsW + ptsD + ptsGD)
            }
        }
    })
</script>

</html>
