import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ItemsLostDetailTimelineComponent } from './items-lost-detail-timeline.component';

describe('ItemsLostDetailTimelineComponent', () => {
  let component: ItemsLostDetailTimelineComponent;
  let fixture: ComponentFixture<ItemsLostDetailTimelineComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ItemsLostDetailTimelineComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ItemsLostDetailTimelineComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
